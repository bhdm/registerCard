<?php
namespace Crm\MainBundle\Abby;

class Recognition{

    protected $applicationId;

    protected $password;

    protected $filename;

    protected $path;

    protected $xml;

    protected $data;

    public function __construct($fileName){
        $this->applicationId = 'taxoCard';
        $this->password = 'ILCL63gtPIQP7GOXDlqgHS4F';
//        $this->path = '/var/www/crm/web/upload/docs';
        $this->path = $fileName;
        $this->data = array();
    }

    /**
     * @param mixed $applicationId
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;
    }

    /**
     * @return mixed
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setXml($xml)
    {
        $this->xml = $xml;
    }

    /**
     * @return mixed
     */
    public function getXml()
    {
        return $this->xml;
    }

    public function getRequestXml($file = NULL){
        if ( $file === null ){
            $file = $this->getFilename();
        }

        $filePath = $this->path;
        if(!file_exists($filePath))
        {
            return 'error: Ошибка файла';
        }
        if(!is_readable($filePath) )
        {
            die('Access to file '.$filePath.' denied.');
        }

        // Recognizing with English language to rtf
        // You can use combination of languages like ?language=english,russian or
        // ?language=english,french,dutch
        // For details, see API reference for processImage method
        $url = 'http://cloud.ocrsdk.com/processImage?language=russian&exportFormat=xml';

        // Send HTTP POST request and ret xml response
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_USERPWD, "$this->applicationId:$this->password");
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        curl_setopt($curlHandle, CURLOPT_USERAGENT, "PHP Cloud OCR SDK Sample");
        $post_array = array(
            "my_file"=>"@".$filePath,
        );
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $post_array);
        $response = curl_exec($curlHandle);
        if($response == FALSE) {
            $errorText = curl_error($curlHandle);
            curl_close($curlHandle);
            die($errorText);
        }
        $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        curl_close($curlHandle);

        // Parse xml response
        $xml = simplexml_load_string($response);
        if($httpCode != 200) {
            if(property_exists($xml, "message")) {
                die($xml->message);
            }
            return "unexpected response ".$response;
        }

        $arr = $xml->task[0]->attributes();
        $taskStatus = $arr["status"];
        if($taskStatus != "Queued") {
            return "Unexpected task status ".$taskStatus;
        }

        // Task id
        $taskid = $arr["id"];

        // 4. Get task information in a loop until task processing finishes
        // 5. If response contains "Completed" staus - extract url with result
        // 6. Download recognition result (text) and display it

        $url = 'http://cloud.ocrsdk.com/getTaskStatus';
        $qry_str = "?taskid=$taskid";

        // Check task status in a loop until it is finished
        // TODO: support states indicating error
        while(true)
        {
            sleep(5);
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, $url.$qry_str);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_USERPWD, "$this->applicationId:$this->password");
            curl_setopt($curlHandle, CURLOPT_USERAGENT, "PHP Cloud OCR SDK Sample");
            $response = curl_exec($curlHandle);
            $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
            curl_close($curlHandle);

            // parse xml
            $xml = simplexml_load_string($response);
            if($httpCode != 200) {
                if(property_exists($xml, "message")) {
                    die($xml->message);
                }
                return "Unexpected response ".$response;
            }
            $arr = $xml->task[0]->attributes();
            $taskStatus = $arr["status"];
            if($taskStatus == "Queued" || $taskStatus == "InProgress") {
                // continue waiting
                continue;
            }
            if($taskStatus == "Completed") {
                // exit this loop and proceed to handling the result
                break;
            }
            if($taskStatus == "ProcessingFailed") {
                die("Task processing failed: ".$arr["error"]);
            }
            die("Unexpected task status ".$taskStatus);
        }

        // Result is ready. Download it

        $url = $arr["resultUrl"];
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        // Warning! This is for easier out-of-the box usage of the sample only.
        // The URL to the result has https:// prefix, so SSL is required to
        // download from it. For whatever reason PHP runtime fails to perform
        // a request unless SSL certificate verification is off.
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curlHandle);
        curl_close($curlHandle);

        // Let user donwload rtf result
//        header('Content-type: application/xml');
//        header('Content-Disposition: attachment; filename="file.xml"');
//        $this->xml  = new \SimpleXMLElement($response);
        $this->xml  = $response;
//        echo $response;
//        exit;
//        $this->xml = file_get_contents('')
        return $this;
    }

    public function getRequestRow($numRow = 0){
        if ($this->xml == NULL ){
            return 'error: XML Не получен';
        }

        $xml = $this->xml;
        $txt = '';
        while (true){
            $i = 0;
            if (isset($xml->page->block[$numRow]->text)){
                if (!is_array($xml->page->block[$numRow]->text->par)){
                    foreach ( $xml->page->block[$numRow]->text->par->line->formatting->charParams as $charset){
                        $txt .= $charset;
                    }
                }else{
//                    foreach ()
//                        foreach ( $xml->page->block[$numRow]->text->par->line->formatting->charParams as $charset){
//                            $txt .= $charset;
//                        }
                }

                return $txt;
            }else{
                $i++;
                if ($i > 10){
                    return $txt;
                }
                $numRow++;
            }
        }
        return $txt;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    public function objectToArray($object)
    {
        if (is_object($object))
        {
            $object = get_object_vars($object);
        }

        if (is_array($object))
        {
            return array_map( array($this, __FUNCTION__), $object );
        }
        else
        {
            return $object;
        }
    }

}