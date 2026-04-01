<?php

namespace App\Http\Controllers\Api\V1\File;

use App\Http\Controllers\Controller;
use App\Services\Storage\FileManager;
use Illuminate\Http\Request;


class FilesController extends Controller {
	private $service;

	public function __construct( FileManager $service ) {
		$this->service = $service;
	}

	public function fileUpload(Request $request)
	{
		$upload = $this->service->upload($request->file, $request->get('upload_path', 'uploads'));
		if(!$upload){
			return response()->json(['success'=>'false','data' => 'Failed to file upload']);
		}
		return response()->json(['success'=>'true','data' => $upload]);
	}

	public function fileRemove(Request $request)
	{
        $result = $this->service->delete($request->file);
		if(!$result){
			return response()->json(['success'=>'false','data' => 'Failed to file upload']);
		}
		return response()->json(['success'=>'true','data' => 'File deleted successfully']);
	}

	public function vehicleImageUpload(Request $request) {

	}
}


