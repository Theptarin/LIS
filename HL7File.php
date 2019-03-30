<?php
require_once "HL7Segment.php";
//namespace Orr;

/**
 * Description of HL7
 * @author Suchart Bunhachirat <suchartbu@gmail.com>
 */
class HL7File {

    /**
     * เก็บอาเรย์จากสตริง HL7
     */
    private $seg = array();

    /**
     * พาทและชื่อไฟล์ HL7
     */
    //private $filename = "";

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
        $this->seg = array_filter(explode("\r\n", $string));

        if (substr($this->seg[0], 0, 3) == 'MSH') {
            $i = 0;
            foreach ($this->seg as $value) {
                $seg = explode("|", $value, 2);
                /**
                 * @todo เช็คจำนวนอะเรย์ต้องเท่ากับ 2 เพื่อป้องกันปัญหามีค่าว่างหลงมา
                 */
                if (count($seg) == 2) {
                    $segment = new HL7Segment();
                    $segment->name = $seg[0];
                    $segment->index = $i;
                    $segment->fields = explode("|", $seg[1]);
                    $this->set_segemet_count($segment->name);
                    $this->message[] = $segment;
                    $i ++;
                }
            }
        } else {
            throw new Exception('Invalid HL7 Message must start with MSH.');
        }
    }

    /**
     * คืนค่าอะเรย์ตามบรรทัดในไฟล์ HL7 ตามโครงสร้าง hl7_segment
     * @return type array
     */
    public function get_message() {
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
