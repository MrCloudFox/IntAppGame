<?php

class Model{

	// здесь будем хранить название файла данных для этой модель
	public $dataFileName;

	public function __construct($modelName){
		// конструируем название файла данных
		// должно получиться примерно /data/modelname.json
		$this->dataFileName=DATA_FOLDER.DS.$modelName.'.json';
	}
	
	// общие методы для всех моделей

	public function load($id=false){
		// считаем файл
		$data=file_get_contents($this->dataFileName);
		// декодируем 
		$data=json_decode($data);

		// если id не передан - то возвращаем все записи, иначе только нужную
		if($id===false){
			return $data;
		}else{
			if(array_key_exists($id, $data)){
				return $data[$id];	
			}	
		}
		return false;
	}


	public function create(array $item){
		// считываем нашу "базу данных"
		$data=file_get_contents($this->dataFileName);
		// декодируем
		$data=json_decode($data, true);
		// добавляем элемент
		array_push($data, $item);
		// сохраняем файл, и возврfщаем результат сохранения (успех или провал)
		return file_put_contents($this->dataFileName, json_encode($data));
	}


	public function save($id, $item){
		$data=file_get_contents($this->dataFileName);
		
		$data=json_decode($data, true);
		
		if(array_key_exists($id, $data)){
			$data[$id] = $item;
		}
		
		return file_put_contents($this->dataFileName, json_encode($data));
		
	}


	public function delete($id){
		$data=file_get_contents($this->dataFileName);
		
		$data=json_decode($data, true);
		
		if(array_key_exists($id, $data)){
			unset($data[$id]);
		}
		
		return file_put_contents($this->dataFileName, json_encode($data));
	}
}
