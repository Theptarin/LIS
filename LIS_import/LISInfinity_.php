<?php

/**
 * การอ่านไฟล์ข้อมูลผลแลปผู้ป่วยจาก LIS Infinity
 * 1. อ่านไฟล์ HL7 ผลแลปอยู่ในโฟลเดอร์
 * 2. วิเคราะห์ไฟล์แยกส่วนข้อมูลเพื่อสามารถจัดเตรียมนำเข้าฐานข้อมูลได้
 * 3. ส่งข้อมูลเข้าฐานข้อมูล
 * @author สุชาติ บุญหชัยรัตน์ suchart bunhachirat <suchartbu@gmail.com>
 * @link https://drive.google.com/file/d/0B9r7oU4ZCTVJcnhteF9YSUF3Q0k/view?usp=sharing รายละเอียด HL7
 */
require_once 'InfinityToMySQL.php';

class LISInfinity {

    private $pathDoneTo = "/home/lis/History/";
    private $pathErrorTo = "/home/lis/History/";

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
            $hl7_2_db = new InfinityToMySQL($filename);
            /**
             * ย้ายไฟล์ตามสถานะ
             */
            if ($hl7_2_db->error_message == null) {
                $this->copy_to_hims($filename);
                $this->move_done_file($filename);
            } else {
                $this->copy_to_hims($filename);
                $this->move_error_file($filename);
                echo $hl7_2_db->error_message . "\n";
            }
        }
    }

    /**
     * ส่งไฟล์ให้ HIMs
     * @param string $filename
     */
    private function copy_to_hims($filename) {
        try {
            copy($filename, "/var/www/mount/hims-doc/lis/Result/" . basename($filename));
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

/**
 * find ./ -type f -exec cp '{}' ../ResultForTheptarin/ \;
 * https://ubuntuforums.org/showthread.php?t=1385966
 */
//$my = new LISInfinity("/var/www/mount/hims-doc/lis/ResultForTheptarin/*.hl7");
$my = new LISInfinity("/home/lis/Result/*.hl7");
