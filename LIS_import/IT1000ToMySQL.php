<?php

require_once "HL7File.php";

/**
 * นำเข้าข้อมูลจาก LIS Cobas IT1000
 * 1. อ่านไฟล์โดยใช้คลาส hl7
 * 2. เชื่อมฐานข้อมูล
 * 3. เพิ่มรายการใหม่ใน theptarin_utf8 lis_glu_order lis_glu_result
 * @author suchart bunhachirat
 */
class IT1000ToMySQL {

    private $path_filename;
    private $hl7;
    private $conn = null;
    public $error_message = null;

    /**
     * รับค่าพาธไฟล์ HL7
     * @param string $path_filename
     */
    public function __construct($path_filename) {
        $this->path_filename = $path_filename;
        try {
            $this->hl7 = new HL7File($path_filename, "\r");
            $this->insert_order();
        } catch (Exception $ex) {
            echo 'Caught exception: ', $ex->getMessage(), "\n";
        }
    }

    /**
     * เชื่อมฐานข้อมูลที่ต้องการใช้งาน
     */
    private function get_conn() {
        $dsn = 'mysql:host=10.1.99.19;dbname=theptarin';
        $username = 'theptarin';
        $password = 'orr-projects';
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );

        try {
            $this->conn = new PDO($dsn, $username, $password, $options);
        } catch (Exception $ex) {
            echo "Could not connect to database : " . $ex->getMessage(), "\n";
            exit();
        }
    }

    /**
     * เพิ่มรายการใหม่ใน lis_glu_order
     */
    protected function insert_order() {
        $this->get_conn();
        $message = $this->hl7->get_message();
        $sql = "INSERT INTO lis_glu_order (message_date, patient_id, lis_number, reference_number, accept_time,request_div) VALUES (:message_date, :patient_id, :lis_number, :reference_number, :accept_time,:request_div) ON DUPLICATE KEY UPDATE message_date = :message_date , accept_time = :accept_time ";
        $stmt = $this->conn->prepare($sql);

        if ($stmt) {
            $result = $stmt->execute(array(":message_date" => $message[0]->fields[5], ":patient_id" => $message[1]->fields[2], ":lis_number" => $message[4]->fields[1], ":reference_number" => $message[3]->fields[1], ":accept_time" => $message[3]->fields[8], ":request_div" => substr($message[2]->fields[18], 3)));

            if ($result) {
                $this->read_result($message[4]->fields[1]);
                //print $message[4]->fields[1];
            } else {
                $error = $stmt->errorInfo();
                //echo 'Query failed with message: ' . $error[2];
                $this->error_message .= " insert_order : " . $error[2];
            }
        }
    }

    /**
     * เลือกอ่าน segment ชื่อ OBX คือผลแต่ละรายการ และ NTE สำหรับหมายเหตุ
     * @param int $lis_number
     */
    protected function read_result($lis_number) {
        $message = $this->hl7->get_message();
        /**
         * คำสั่งคัดเฉพาะ secment ที่ต้องการ
         */
        foreach ($message as $value) {
            /**
             * @todo ถ้า$value->name มีขึ้นบรรทัดใหม่ต่อท้ายจะทำให้เช็คไม่เจอ ควรป้องกันปัญหานี้ต่อไป
             */
            switch ($value->name) {
                case "OBX":
                    if (!is_null($remark)) {
                        $remark = $this->insert_result_remark($lis_number, $lis_code, $remark);
                    }
                    $lis_code = $this->insert_result($lis_number, $value);
                    break;
                case "NTE":
                    $remark .= $value->fields[2] . "\n";
                    break;
                default :
                    $lis_code = 0;
                    $remark = NULL;
            }
        }
    }

    /**
     * เพิ่มรายการใหม่ใน lis_glu_result
     * @param int $lis_number
     * @param array $message
     * @return int
     */
    protected function insert_result($lis_number, $message) {

        $test = explode("^", $message->fields[2], 4);
        $validation_time = explode("^", $message->fields[14], 2);
        /* @var $result_date DATE */
        $result_date = date('Y-m-d', strtotime($validation_time[1]));

        $sql = "REPLACE INTO lis_glu_result (lis_number, lis_code, test, lab_code, result_code , result,  unit, normal_range, user_id, technical_time, medical_time, result_date) VALUES (:lis_number, :lis_code, :test, :lab_code, :result_code, :result, :unit, :normal_range, :user_id, :technical_time, :medical_time, :result_date)";
        $stmt = $this->conn->prepare($sql);
        /**
         * @todo  lab_type จากไฟล์ HL7 ไม่มี แต่แก้ไขให้มีในตารางตามเดิม
         * */
        if ($stmt) {
            $result = $stmt->execute(array(":lis_number" => $lis_number, ":lis_code" => $test[0], ":test" => $test[1], ":lab_code" => $test[2], ":result_code" => (is_null($test[3])) ? "" : $test[3], ":result" => $message->fields[4], ":unit" => $message->fields[5], ":normal_range" => $message->fields[6], ":technical_time" => $validation_time[0], ":medical_time" => $validation_time[1], ":user_id" => $message->fields[15], ":result_date" => $result_date));

            if ($result) {
                return $test[0];
            } else {
                $error = $stmt->errorInfo();
                //echo 'Query failed with message: ' . $error[2];
                $this->error_message .= " insert_result : " . $error[2];
            }
        }
    }

    /**
     * เพิ่มข้อมูลที่ Remark ของ Test
     * @param int $lis_number
     * @param int $lis_code
     * @param string $remark
     * @return int
     */
    protected function insert_result_remark($lis_number, $lis_code, $remark) {

        $sql = "UPDATE `lis_glu_result` SET `remark`= :remark WHERE `lis_number` = :lis_number AND `lis_code` = :lis_code";
        $stmt = $this->conn->prepare($sql);

        if ($stmt) {
            $result = $stmt->execute(array(":lis_number" => $lis_number, ":lis_code" => $lis_code, ":remark" => $remark));
            if ($result) {
                //print $remark;
                return null;
            } else {
                $error = $stmt->errorInfo();
                //echo 'Query failed with message: ' . $error[2];
                $this->error_message .= "  insert_result_remark : " . $error[2];
            }
        }
    }

}
