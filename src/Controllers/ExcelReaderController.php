<?php

namespace Deviny\Excelify\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Validator;
use Storage;

class ExcelReaderController extends Controller
{

    public function index(){
        return view('excelify::index')
        ->with('tabnum',1);
    }
    
    public function __invoke(Request $r){

        //記住input
        $r->flash();
        session(['tabnum' => '1']);

        //路徑初值
        $path="";
        if (!isset($r->excelfile)&&$r->session()->has('path')) {
            $path = session('path');
        }else{
            //$path = $r->file('excelfile')->store('files', 'local', 'temp.xls');
            //固定的檔案
            if(!is_null($r->excelfile)){
              $path = Storage::putFileAs('excelfile',$r->file('excelfile') , 'temp.xlsx');
              session(['path'=>$path]);
            }
        }

      //Input的資料驗證
      $validator = Validator::make($r->all(), [
        'start' => 'required',
        'sheetnum' => 'required|numeric',
        ],[
        'start.required'=>'start|'.__('message.start_required'),
        'sheetnum.required'=>'sheetnum|'.__('message.sheet_can_not_be_empty'),
        ]);

      //更多的檢測
        $validator->after(function ($validator) use ($r, $path) {
          if (!preg_match('/^[a-zA-Z]+\\d+$/uis', $r->start)) {
            $validator->errors()->add('start', 'start|'.__('message.start_cell_error'));
          }
          if ($r->sheetnum==0) {
            $validator->errors()->add('sheetnum', 'sheetnum|'.__('message.sheet_can_not_be_zero'));
          }

          if(empty($path)) {
            $validator->errors()->add('excelfile', 'excelfile|'.__('message.select_a_excel_file'));
          }

          if(!file_exists(storage_path()."/app/".$path)){
            $validator->errors()->add('excelfile', 'excelfile|'.__('message.select_a_excel_file'));
          }
          //副檔名驗證
          if(!is_null($r->excelfile)){
            $originalName = $r->file('excelfile')->getClientOriginalName();
            if(!preg_match('/\\.(xls|xlt|xlsx|xltx)$/uis', $originalName)){
              $validator->errors()->add('excelfile', 'excelfile|'.__('message.select_a_excel_file'));
            }
          }
          //特定格式應該填寫tablename
          switch($r->datatype){
            case 'qb':
              if(empty($r->tablename))
              $validator->errors()->add('tablename', 'tablename|'.__('message.tablename_required'));
            break;
            case 'sql':
              if(empty($r->tablename))
              $validator->errors()->add('tablename', 'tablename|'.__('message.tablename_required'));
            break;
            case 'json':
              if(empty($r->tablename))
              $validator->errors()->add('tablename', 'tablename|'.__('message.tablename_required'));
            break;
          } 
        })->validate();


        //起始欄位
        $start_column = strtoupper(preg_replace('/(\\w+)(\\d+)/um', '$1', $r->start));
        //起始行數
        $start_row = preg_replace('/^([a-zA-Z])(\\d+)$/us', '$2', $r->start);

        $arrFieldName = $r->fieldName; //欄位
        $arrFieldValue = $r->fieldValue; //值
        $arrFieldKvMap = $r->fieldKvMap;

        $sheetnum = $r->sheetnum-1; //初始為0

        $filePath = storage_path(sprintf("app/%s",$path));  
        try {
            $this->objPHPExcel = IOFactory::load($filePath);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($filePath,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        //取得Sheet0
        $sheet = $this->objPHPExcel->getSheet($sheetnum);
        if(!$r->end){
          $highestColumn = $sheet->getHighestColumn(); //最大欄寬的英文，例如: BH
          $highestRow = $sheet->getHighestRow();       //最大的列數
          $r->end=$highestColumn.$highestRow;
      }
      /*
      if(is_null($r->start)){
        $r->start="A1"; 
      }
      */
      //最大欄寬
      if(is_null($r->end)){
         $highestColumn = $sheet->getHighestColumn(); //最大欄寬的英文，例如: BH
         $highestRow = $sheet->getHighestRow(); //最大列數
         $r->end = $highestColumn.$highestRow;
      }

        //依設定的，啟始欄位一行一行存入Array中
      $rowData = $sheet->rangeToArray($r->start.':'.$r->end,
        NULL,
        TRUE,
        FALSE);

      //  dd($rowData);
      //重整
      $rows = [];
      foreach($rowData as $row_num=>$row)
      {
        foreach($row as $column_index=>$column){
            $letters = range('A', 'Z');
            $start_column_index = array_search($start_column, $letters);

            $letter = $this->toLetter($column_index+$start_column_index);

            foreach($arrFieldName as $key_index=>$key){
                if(strtoupper($key)==$letter&&!empty($arrFieldValue[$key_index])){

                    if(is_null($column)) 
                    {
                        $rows[$row_num][$arrFieldValue[$key_index]] = "";
                    }
                    else
                    {
                      //有定義對Kv對印
                      if(!empty(trim($arrFieldKvMap[$key_index]))){
                        $strColumn = preg_replace('/^\\[(.*)\\]$/um', '$1', $arrFieldKvMap[$key_index]);
                        $arrData = explode(",", $strColumn);
                        foreach($arrData as $item){
                           $itemkv = explode("=>",$item); 
                           $result = preg_replace('/^"(.*)"$/um', '$1', trim($itemkv[0]));
                           if($result==$column){
                            $column = preg_replace('/^"(.*)"$/um', '$1', trim($itemkv[1]));
                            //定義新值
                            if($r->datatype=="excel"){
                              $objPHPExcel->getActiveSheet()->setCellValue($arrFieldName[$key_index].($row_num+$start_row), $column); 
                            }
                          }
                        }
                      }
                        $rows[$row_num][$arrFieldValue[$key_index]] = str_replace('"', '\"', $column);
                    }

                }
            }
        }
      }
      //轉存
      if($r->datatype=="excel"){
      $objWriter = new Xlsx($this->objPHPExcel);
      $objWriter->save(storage_path()."/app/excelfile/download.xlsx");
      }
    
    return view('excelify::index',['data'=>$rows,'datatype'=>$r->datatype,'tablename'=>$r->tablename,'tabnum'=>1]);
 }
 //解鎖
 function unlock(Request $r){
     if(config('excelify.secret')==$r->secret){
         session(['excelify_secret'=>$r->secret]);
     }
     return redirect('/');
 }
 function lock(){
    session()->flush();
    return redirect('/');
 }

 //結果下載
 function download(Request $r){
  $file = fopen(storage_path()."/app/file.txt","w");
  fwrite($file,$r->data);
  fclose($file);
  $filename = empty($r->filename)?"file":$r->filename;
  return response()->download(storage_path()."/app/file.txt",$filename.".txt");
 }

 //下載轉存的Excel
 function download_excel(){
  return response()
  ->download(storage_path()."/app/excelfile/download.xlsx", 'download.xlsx');
 }

  function toLetter($i){
        $letters = range('A', 'Z');
        if($i<=25){
            return $letters[$i];
        }
        if($i>25){
            $quot = ($i+1)/26;
            $rem = ($i+1)%26;
            if( $rem == 0){
               return $letters[$quot-2].$this->toLetter($rem+25); 
            }else{
               return $letters[$quot-1].$this->toLetter($rem-1); 
            }
        }
    }
}
