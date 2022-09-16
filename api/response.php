<?php
class Response
{
	public $code;
	public $message;
	public $data;

	// public function code($code){
	//  	$this->code = $code;
	// }

	// public function message($message){
	// 	$this->message = $message;
	// }

	// public function data($data){
	// 	$this->data = $data;
	// }

	function json()
	{
		$res['code'] = $this->code;
		$res['message'] = $this->message;
		$res['data'] = $this->data;
		http_response_code($this->code);
		echo json_encode($res);
	}
}
