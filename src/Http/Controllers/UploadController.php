<?php

namespace Acitjazz\UploadEndpoint\Http\Controllers;

use Acitjazz\UploadEndpoint\Http\Requests\DestroyRequest;
use Acitjazz\UploadEndpoint\Http\Requests\UploadRequest;
use Acitjazz\UploadEndpoint\Http\Resources\CloudResource;
use Acitjazz\UploadEndpoint\Http\Resources\MediaResource;
use Acitjazz\UploadEndpoint\Models\MediaMongo;
use Acitjazz\UploadEndpoint\Models\MediaMysql;
use Aws\S3\Exception\S3Exception;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response ;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{

    /**
     * The Model name.
     *
     * @var string
     */
    protected $model;

    public function __construct()
    {
        $this->model = config('uploadendpoint.database') == 'mongodb' ?  new MediaMongo : new MediaMysql;
    }

    public function index(){
        return view('acitjazz::upload');
    }

    public function getdata($id){
        $media = $this->model->find($id);
        $response = response();

        $response = Response::make($media, 200);
        $response->header('Content-Disposition', 'inline');
        $response->header('filename', $media->url);
        $response->header('Content-Type', $media->file_type);
        return $response;
    }

    public function show(Filesystem $filesystem, $path){
        try {
            $server = ServerFactory::create([
                'response' => new LaravelResponseFactory(app('request')),
                'source' => $filesystem->getDriver(),
                'cache' => $filesystem->getDriver(),
                'cache_path_prefix' => '.cache',
                'base_url' => 'image',
            ]);
            return $server->getImageResponse($path, request()->all());
        }  catch (\League\Glide\Filesystem\FileNotFoundException $err) {
            return abort(404);
        } catch(\Intervention\Image\Exception\NotReadableException $notread)
        {
           return $path;
        }
    }

    public function store(UploadRequest $request)
    {
        try{
            $date = Carbon::now()->format('dmY-his');
            $folder = str()->snake($request->folder);
            $path = config('uploadendpoint.upload_path').'/'.config('uploadendpoint.bucket_name').'/'. $folder;
          
            if (!is_dir($path)) {
            $folder_full  = $path;
            if (!is_dir($folder_full)) mkdir($folder_full, 0777, true);
            }
            $path = $path;
            $download =  $folder . '/';
            $image = $request->file('file');
            $filename =  $request->name == null ? str_replace($image->getClientOriginalExtension(), '', $image->getClientOriginalName()) : $request->name;
            $fileext = str()->slug($filename)  . '-' . $date . '.' .  $image->getClientOriginalExtension();
            $media =  $this->model;
            $media->extension = $image->getClientOriginalExtension();
            $media->file_type = $image->getClientMimeType();
            $media->storage = 'local';
            $media->size = $image->getSize();
            $media->original_name = $fileext;
            $media->public_id = $folder;
            $media->readable_size = ($image->getSize()/1000) . 'KB';
            $media->name = $filename;
            $media->width = getimagesize($image)[0] ?? 0;
            $media->height = getimagesize($image)[1] ?? 0;
            $media->url = $download . $fileext;
            $media->save();
            $image->move($path, $media->url);
            
            Cache::tags(['medias'])->flush();
            return response()->make(new MediaResource($media), 200, [
                'Content-Type' => 'text/plain',
            ]);
            return response()->json(new MediaResource($media));
        }catch (Exception $e){
            $return['status'] = false;
            $return['data'] =  [];
            $return['message'] = $e->getMessage();
            return response()->json($return, 422);
        }
    }

    
    
    public function destroy(DestroyRequest $request)
    {
        try{
            $media = json_decode($request->getContent());
            $media = $this->model->find($media->id ?? request('media_id'));
            if($media){
                Storage::disk('local')->delete(config('uploadendpoint.bucket_name').'/'.$media->url);
                $media->forceDelete();
                Cache::tags(['medias'])->flush();
            }
            return response()->json(true,200);
        }catch (Exception $e){
            $return['status'] = false;
            $return['data'] =  [];
            $return['message'] = $e->getMessage();
            return response()->json($return, 422);
        }
    }



    public function storeAws(UploadRequest $request)
    {

        try{
            $folder = str()->snake($request->folder) ?? 'media';
            $date = Carbon::now()->format('dmY-his');
            $image = $request->file('file');
    
            $filename =  $request->name == null ? str_replace($image->getClientOriginalExtension(), '', $image->getClientOriginalName()) : $request->name;
           
            $filePath = config('uploadendpoint.bucket_name').'/'.$folder.'/' . str()->slug($filename)  . '-' . $date . '.' .  $image->getClientOriginalExtension();
            $media =  $this->model;
            $media->extension = $image->getClientOriginalExtension();
            $media->file_type = $image->getClientMimeType();
            $media->storage = 'local';
            $media->size = $image->getSize();
            $media->original_name = $filePath;
            $media->public_id = $folder;
            $media->readable_size = ($image->getSize()/1000) . 'KB';
            $media->name = $filename;
            $media->width = getimagesize($image)[0];
            $media->height = getimagesize($image)[1];
            $path = Storage::disk('s3')->put($filePath, file_get_contents($image));
            $path = Storage::disk('s3')->url('0');
            $media->url = str_replace('0','',$path) . $filePath;
            $media->save();
            Cache::tags(['medias'])->flush();
            return response()->json(new CloudResource($media), 200);
        }
        catch (S3Exception $e) {
            $return['status'] = false;
            $return['data'] =  [];
            $return['message'] = $e->getMessage();
            return response()->json($return, 422);
        }
    }

    
    public function destroyAws(DestroyRequest $request)
    {
        try{
            $media = $this->model->find($request->media_id);
            Storage::disk('s3')->delete($media->original_name);
            $media->forceDelete();
            Cache::tags(['medias'])->flush();
            return response()->json(true,200);
        }catch (Exception $e){
            $return['status'] = false;
            $return['data'] =  [];
            $return['message'] = $e->getMessage();
            return response()->json($return, 422);
        }
    }


    public function storeCloudinary(UploadRequest $request)
    {
        try{
            $result = $request->file('file')->storeOnCloudinary();
            $media =  $this->model;
            $media->user_id = auth()->user()->id ?? null;
            $media->storage = 'cloudinary';
            $media->url =   $result->getSecurePath(); // Get the url of the uploaded file; https
            $media->size =  $result->getSize(); // Get the size of the uploaded file in bytes
            $media->readable_size = $result->getReadableSize(); // Get the size of the uploaded file in bytes, megabytes, gigabytes or terabytes. E.g 1.8 MB
            $media->file_type = $result->getFileType(); // Get the type of the uploaded file
            $media->name = $request->name ?? $result->getFileName(); // Get the file name of the uploaded file
            $media->original_name = $result->getOriginalFileName(); // Get the file name of the file before it was uploaded to Cloudinary
            $media->public_id =  $result->getPublicId(); // Get the public_id of the uploaded file
            $media->extension =  $result->getExtension(); // Get the extension of the uploaded file
            $media->height = $result->getWidth(); // Get the width of the uploaded file
            $media->width = $result->getHeight(); // Get the height of the uploaded file
            $media->save();
            Cache::tags(['medias'])->flush();
            return response()->json(new CloudResource($media));
        }
        catch (Exception $e) {
            $return['status'] = false;
            $return['data'] =  [];
            $return['message'] = $e->getMessage();
            return response()->json($return, 422);
        }
        // Upload an image file to cloudinary with one line of code
        
    }
    public function destroyCloudinary(DestroyRequest $request)
    {
        try{
            $media = $this->model->find($request->media_id);
            $res =  cloudinary()->destroy($media->public_id);
            $media->forceDelete();
            Cache::tags(['medias'])->flush();
            return response()->json($res,200);
        }
        catch (Exception $e) {
            $return['status'] = false;
            $return['data'] =  [];
            $return['message'] = $e->getMessage();
            return response()->json($return, 422);
        }
    }
}