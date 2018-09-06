<?php
/**
 * Created by PhpStorm.
 * User: jiangbaiyan
 * Date: 2018/8/28
 * Time: 09:13
 */

namespace App\Util;


use Illuminate\Support\Facades\Storage;
use src\Exceptions\OperateFailedException;
use src\Exceptions\ResourceNotFoundException;

class File
{

    const MSG_WRONG_FILE_FORAMT = '不允许上传该格式';

    const UPLOAD_FAILED = '文件上传失败，请重试';

    const UPYUN_HOST = 'https://cloudfiles.cloudshm.com/';

    const ALLOW_FORMAT = ['doc', 'docx', 'pdf', 'DOC', 'DOCX', 'PDF', 'rar', 'zip', 'RAR', 'ZIP', 'xls', 'xlsx', 'XLS', 'XLSX'];//规定允许上传的文件格式

    /**
     * 存储文件并返回文件路径(支持批量存储)
     * @param $file
     * @param string $disk
     * @return array|false|string
     * @throws OperateFailedException
     */
    public static function saveFile($file,$disk = 'upyun')
    {
        $path = [];
        self::isAllowedFormat($file);
        if (is_array($file)) {
            foreach ($file as $fileItem) {
                $filePath = Storage::disk($disk)->putFileAs('/tis/' . date('Y') . '/' . date('md'),$fileItem,$fileItem->getClientOriginalName(),'public');
                if (empty($filePath)){
                    throw new OperateFailedException(self::UPLOAD_FAILED);
                }
                if ($disk == 'upyun'){
                    $path[] = self::UPYUN_HOST . $filePath;
                }else{
                    $path[] = $filePath;
                }
            }
        } else {
            $filePath = Storage::disk($disk)->putFileAs('/tis/' . date('Y') . '/' . date('md'),$file,$file->getClientOriginalName(),'public');
            if (empty($filePath)){
                throw new OperateFailedException(self::UPLOAD_FAILED);
            }
            if ($disk == 'upyun'){
                $path = self::UPYUN_HOST . $filePath;
            }else{
                $path = $filePath;
            }
        }
        if (is_array($path)){
            return implode(',',$path);
        }
        return $path;
    }

    /**
     * 判断文件格式是否符合要求
     * @param $file
     * @throws OperateFailedException
     */
    public static function isAllowedFormat($file)
    {
        if (is_array($file)) {
            foreach ($file as $fileItem) {
                $ext = $fileItem->getClientOriginalExtension();
                if (!in_array($ext, self::ALLOW_FORMAT)) {
                    throw new OperateFailedException(self::MSG_WRONG_FILE_FORAMT);
                }
            }
        } else {
            $ext = $file->getClientOriginalExtension();
            if (!in_array($ext, self::ALLOW_FORMAT)) {
                throw new OperateFailedException(self::MSG_WRONG_FILE_FORAMT);
            }
        }
    }

    /**
     * 删除文件
     * @param $path
     * @throws OperateFailedException
     * @throws ResourceNotFoundException
     */
    public static function deleteFile($path){
        if (empty($path)){
            return;
        }
        $filePath = '/home/wwwroot/TeacherInfoSystem/storage/app/public/' . $path;
        if (!file_exists($filePath)){
            throw new ResourceNotFoundException();
        }
        try {
            unlink($filePath);
        } catch (\Exception $e){
            throw new OperateFailedException($e->getMessage());
        }
    }
}