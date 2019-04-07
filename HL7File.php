<?php

require_once "HL7Segment.php";

/**
 * Description of HL7
 * @author Suchart Bunhachirat <suchartbu@gmail.com>
 */
class HL7File {

    /**
     * เก็บอาเรย์จากสตริง HL7
     */
    private $lines = array();
    
    /**
     * HL7 Segment name
     * @var array 
     */
    private $segmentNames = ["MSH", "PID", "PV1", "ORC", "OBR", "NTE", "OBX", "SPM"];

    /**
     * ออบเจ็คแต่ละ segment
     */
    private $message = array();

    /**
     * นับรายการแยกแต่ละ segment
     */
    public $segment_count = array();

    /**
     * ตรวจหา 'MSH' ส่วนแรกของสตริง HL7 Message
     * @access protected
     */
    public function __construct($filename) {
        $this->load($filename);
    }

    /**
     * โหลดไฟล์ HL7
     */
    public function load($filename) {
        if (file_exists($filename)) {
            try {
                $myfile = fopen($filename, "r");
                $this->set_content(fread($myfile, filesize($filename)));
                fclose($myfile);
            } catch (Exception $ex) {
                echo 'HL7 load exception: ', $ex->getMessage(), "\n";
            }
        } else {
            throw new Exception('file not exists!');
        }
    }

    /**
     * อ่านไฟล์ HL7
     */
    protected function set_content($string) {
        $this->lines = array_filter(explode("\r\n", $string));

        if (substr($this->lines[0], 0, 3) == 'MSH') {
            $i = 0;
            foreach ($this->lines as $value) {
                $segments = explode("|", $value, 2);
                if (count($segments) == 2 AND in_array($segments[0], $this->segmentNames)) {
                    $this->message[] = $this->getHL7Segment($i, $segments);
                    $i ++;
                } else {
                    /**
                     * แก้ไขปัญหาการขึ้นบรรทัดใหม่ก่อนเริ่ม segment ใหม่
                     */
                    $fixValues = $this->lines[$i - 1] . " " . $value;
                    //print_r($fixValues);
                    $segments = explode("|", $fixValues, 2);
                    $this->message[$i - 1] = $this->getHL7Segment($i - 1, $segments);
                }
            }
        } else {
            throw new Exception('Invalid HL7 Message must start with MSH.');
        }
    }
    
    /**
     * คืนค่า HL7Segment
     * @param integer $i
     * @param array $values
     * @return \HL7Segment
     */
    private function getHL7Segment($i, $values) {
        $segment = new HL7Segment();
        $segment->name = $values[0];
        $segment->index = $i;
        $segment->fields = explode("|", $values[1]);
        $this->set_segemet_count($segment->name);
        return $segment;
    }

    /**
     * คืนค่าอะเรย์ตามบรรทัดในไฟล์ HL7 ตามโครงสร้าง hl7_segment
     * @return type array
     */
    public function get_message() {
        //print_r($this->message);
        return $this->message;
    }

    /**
     * นับรายการแต่ละ segment
     */
    private function set_segemet_count($key) {
        if (array_key_exists($key, $this->segment_count)) {
            $this->segment_count[$key] ++;
        } else {
            $this->segment_count[$key] = 1;
        }
    }

}
