<?php
	namespace dackou\service\Meitu;

	class MeituService{
		private $key;
		private $secret;
		private $basicDateFormat = "Ymd\THis\Z";
		private $algorithm = "SDK-HMAC-SHA256";
		private $headerXDate = "X-Sdk-Date";
		private $headerHost = "Host";
		private $headerAuthorization = "Authorization";
		private $headerContentSha256 = "X-Sdk-Content-Sha256";

		public function __construct(string $key,string $secret)
		{
			$this->key = $key;
			$this->secret = $secret;
		}

		public function signStringToSign($stringToSign, $signingKey)
	    {
	        $hm = hash_hmac("sha256", $stringToSign, $signingKey, true);
	        return bin2hex($hm);
	    }

	    public function authHeaderValue($signature, $accessKey, $signedHeaders)
	    {
	        $signedHeadersStr = implode(";", $signedHeaders);
	        $headerValue = sprintf("%s Access=%s, SignedHeaders=%s, Signature=%s", $this->algorithm, $accessKey, $signedHeadersStr, $signature);
	        $encodeVal = base64_encode($headerValue);
	        
	        return "Bearer " . $encodeVal;
	    }

	    public function canonicalRequest($method, $url, $headers, $body, $signedHeaders)
	    {
	        $canonicalURI = $this->canonicalURI(parse_url($url, PHP_URL_PATH));
	        $query = parse_url($url, PHP_URL_QUERY);
	        parse_str($query, $params);
	        ksort($params);
	        $canonicalQueryString = http_build_query($params);
	        $canonicalHeaders = $this->canonicalHeaders($headers, $signedHeaders);
	                $signedHeadersStr = implode(";", $signedHeaders);
	        $hexencode = isset($headers[$this->headerContentSha256]) && $headers[$this->headerContentSha256]!='' ? $headers[$this->headerContentSha256] : hash("sha256", $body);
	        return sprintf("%s\n%s\n%s\n%s\n%s\n%s",
	            $method, $canonicalURI, $canonicalQueryString, $canonicalHeaders,
	            $signedHeadersStr, $hexencode);
	    }

	    public function canonicalURI($path) {
	        if (strlen($path) == 0 || substr($path, -1) !== '/') {
	            $path .= '/';
	        }
	        return $path;
	    }
	    
	    public function canonicalHeaders($headers, $signedHeaders)
	    {
	        $lowheaders = array();
	        foreach ($headers as $key => $value) {
	            $lowheaders[strtolower($key)] = trim($value);
	        }
	        $a = array();
	        foreach ($signedHeaders as $key) {
	            array_push($a, $key . ':' . $lowheaders[$key]);
	        }
	        return join("\n", $a) ;
	    }


	    public function signedHeaders($headers)
	    {
	        $signedHeaders = [];
	        foreach ($headers as $header => $value) {
	            $signedHeaders[] = strtolower($header);
	        }
	        sort($signedHeaders);
	        return $signedHeaders;
	    }

	    public function sign($url, $method, $headers, $body)
	    {
	        $dt = $headers[$this->headerXDate] ?? '';
	        if (empty($dt)) {
	            $t = new DateTime('now', new DateTimeZone('UTC'));
	            $headers[$this->headerXDate] = $t->format($this->basicDateFormat);
	        } else {
	            $t = DateTime::createFromFormat($this->basicDateFormat, $dt, new DateTimeZone('UTC'));
	        }
	        $signedHeaders = $this->signedHeaders($headers);
	        $canonicalRequest = $this->canonicalRequest($method, $url, $headers, $body, $signedHeaders);
	        $stringToSign = $this->stringToSign($canonicalRequest, $t->format($this->basicDateFormat));
	        $signature = $this->signStringToSign($stringToSign, $this->Secret);
	        $authValue = $this->authHeaderValue($signature, $this->Key, $signedHeaders);
	        $headers[$this->eaderAuthorization] = $authValue;

	        $headersTmp = [];
	        foreach($headers as $key => $value) {
	            $headersTmp[] = $key . ': ' . $value;
	        }

	        $curl = curl_init();
	        curl_setopt_array($curl, array(
	            CURLOPT_CUSTOMREQUEST => $method,
	            CURLOPT_URL => $url,
	            CURLOPT_RETURNTRANSFER => true,
	            CURLOPT_HTTPHEADER => $headersTmp,
	            CURLOPT_POSTFIELDS => $body,
	            CURLOPT_HEADER => true,
	        ));

	        return $curl;
	    }
	    
	    public function stringToSign($canonicalRequest, $timeFormat)
	    {
	        $hash = hash("sha256", $canonicalRequest, true);
	        return sprintf("%s\n%s\n%s",
	            $this->algorithm, $timeFormat, bin2hex($hash));
	    }
	}
?>