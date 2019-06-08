<?php

/**
 * การอ่านไฟล์ข้อมูลเจาะน้ำตาลปลายนิ้ว (เพื่อนำเข้าข้อมูลย้อนหลัง ไม่ทำสำเนา HL7 ส่ง HIMs)
 * 1. อ่านไฟล์ HL7 ผลแลปอยู่ในโฟลเดอร์
 * 2. วิเคราะห์ไฟล์แยกส่วนข้อมูลเพื่อสามารถจัดเตรียมนำเข้าฐานข้อมูลได้
 * 3. ส่งข้อมูลเข้าฐานข้อมูล
 * 4. 
 * @author สุชาติ บุญหชัยรัตน์ suchart bunhachirat <suchartbu@gmail.com>
 * @link https://drive.google.com/file/d/0B9r7oU4ZCTVJcnhteF9YSUF3Q0k/view?usp=sharing รายละเอียด HL7
 */
require_once 'IT1000ToMySQL.php';

class LISIT1000 {

    private $pathCopyTo = "/home/it/glu_copy/";
    private $pathDoneTo = "/home/it/poct_glu/History/";
    private $pathErrorTo = "/home/it/poct_glu/History/";

    /**
     * รับค่าพาธโฟลเดอร์ HL7
     * @param string $path_foder
     */
    public function __construct($path_foder) {
        $list_files = glob($path_foder);
        $this->pathDoneTo .= date('Ymd') . "/done";
        $this->pathErrorTo .= date('Ymd') . "/error";
        if (!file_exists($this->pathDoneTo)) {
            mkdir($this->pathDoneTo, 0744, true);
        }

        if (!file_exists($this->pathErrorTo)) {
            mkdir($this->pathErrorTo, 0744, true);
        }

        foreach ($list_files as $filename) {
            printf("$filename size " . filesize($filename) . "  " . date('Ymd H:i:s') . "\n");
            $this->copy_file($filename);
            $hl7_2_db = new IT1000ToMySQL($filename);
            /**
             * ย้ายไฟล์ตามสถานะ
             */
            if ($hl7_2_db->error_message == null) {
                $this->move_done_file($filename);
            } else {
                $this->move_error_file($filename);
                echo $hl7_2_db->error_message . "\n";
            }
        }
    }
    /**
     * คัดลอกไฟล์เพื่อสร้าง Request ที่ HIS
     * @param type $filename
     */
    private function copy_file($filename){
        try {
            copy($filename, $this->pathCopyTo . "/" . basename($filename));
        } catch (Exception $ex) {
            echo 'Caught exception: ', $ex->getMessage(), "\n";
        }
        }

    /**
     * ย้ายไฟล์ที่ประมาลผลสำเร็จ
     * @param string $filename
     */
    private function move_done_file($filename) {
        try {
            rename($filename, $this->pathDoneTo . "/" . basename($filename));
        } catch (Exception $ex) {
            echo 'Caught exception: ', $ex->getMessage(), "\n";
        }
    }

    /**
     * ย้ายไฟล์ที่ประมาลผลไม่สำเร็จ
     * @param string $filename
     */
    private function move_error_file($filename) {
        try {
            rename($filename, $this->pathErrorTo . "/" . basename($filename));
        } catch (Exception $ex) {
            echo 'Caught exception: ', $ex->getMessage(), "\n";
        }
    }

}

/**cd 
 * find ./ -type f -exec cp '{}' ../ResultForImport/ \;
 * https://ubuntuforums.org/showthread.php?t=1385966
 */
//$my = new LISInfinity("/var/www/mount/hims-doc/lis/ResultForTheptarin/*.hl7");
$my = new LISIT1000("/home/it/ResultForImport/*.HL7");
