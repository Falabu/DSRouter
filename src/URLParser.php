<?php


namespace DSRouter;


class URLParser
{
    private $url;
    private $serverUrl;
    private $urlArray;
    private $host;
    private $file;
    private $params;

    public function __construct($url = null, $filename = null)
    {
        $this->host = $_SERVER['HTTP_HOST'];
        $this->serverUrl = $_SERVER['REQUEST_URI'];
        $this->urlArray = explode("/", $this->serverUrl);
        if ($url == null && $filename == null) {
            $this->file = $this->getFileName();
            $this->url = $this->getUrlName();
        } else {
            $this->url = $url;
            $this->file = $filename;
        }
    }

    private function getUrlName()
    {
        $urlTmp = '';
        for ($i = 0; $i < count($this->urlArray) - 1; $i++) {
            $urlTmp .= $this->urlArray[$i] . "/";
        }
        return $urlTmp;
    }

    private function getFileName()
    {
        $filearray = end($this->urlArray);
        if (strpos($filearray, "?")) {
            $newfile = explode("?", $filearray);
            return $newfile[0];
        }
        return $filearray;
    }

    public function addParam($params)
    {
        if (is_array($params)) {
            $this->params .= "?";
            $counter = 0;
            foreach ($params as $param => $key) {
                $this->params .= $param . "=" . $key;
                if (count($params) - 1 !== $counter) {
                    $this->params .= "&";
                }
                $counter++;
            }
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getUrlWithHost()
    {
        return $this->host . $this->url . $this->file . $this->params;
    }

    /**
     * @return mixed
     */
    public function getUrlWithoutHost()
    {
        return $this->url . $this->file . $this->params;
    }

    public function __toString()
    {
        return $this->url . $this->file . $this->params;
    }

    public function getURL(){
        return substr($this->url, 0, -1);
    }

    public function getLastParam(){
        return explode('?', end($this->urlArray))[0];
    }
}