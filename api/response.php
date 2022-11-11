<?php
class Response
{
	public $code;
	public $message;
	public $data;

	function json()
	{
		$res['code'] = $this->code;
		$res['message'] = $this->message;
		$res['data'] = $this->data;
		http_response_code($this->code);
		echo json_encode($res);
	}

	public function sukses($code)
	{
		switch ($code) {
			case 200:
				echo json_encode([
					'code' => $code,
					'message' => 'sukses',
					'data' => $this->data
				]);
				http_response_code($code);
				die();
				break;
			case 201:
				echo json_encode([
					'code' => $code,
					'message' => 'Berhasil disimpan',
					'data' => $this->data
				]);
				http_response_code($code);
				die();
				break;
		}
	}

	public function error($code)
	{
		switch ($code) {
			case 400:
				echo json_encode([
					'status' => $code,
					'message' => 'Permintaan buruk',
					'data' => $this->data
				]);
				http_response_code($code);
				die();
				break;
			case 404:
				echo json_encode([
					'status' => $code,
					'message' => 'data tidak ditemukan',
					'data' => $this->data
				]);
				http_response_code($code);
				die();
				break;
			case 403:
				echo json_encode([
					'status' => $code,
					'message' => 'Dilarang',
					'data' => $this->data
				]);
				http_response_code($code);
				die();
				break;
			case 410:
				echo json_encode([
					'status' => $code,
					'message' => 'Hilang',
					'data' => $this->data
				]);
				http_response_code($code);
				die();
				break;
			case 500:
				echo json_encode([
					'status' => $code,
					'message' => 'Kesalahan server dari dalam',
					'data' => $this->data
				]);
				http_response_code($code);
				die();
				break;
		}
	}
}
