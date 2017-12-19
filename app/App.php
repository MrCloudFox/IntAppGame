<?php

class App{

	// контроллер по умолчанию index
	private $controller='index';
	// действие по умолчанию
	private $action='index';
	// 
	private $request=array();
	private $response=array();	


	// метод вызывающийся при создании класса
	public function __construct(){
		
		// определяем контроллер
		if(isset($_GET['controller'])){
			$this->controller=$_GET['controller'];
		}

		// выбор действия по типу HTTP метода
		switch ($_SERVER["REQUEST_METHOD"]) {
			case 'GET':
				if(isset($_GET['id'])){
					$this->action='view';	
				}else{
					$this->action='index';	
				}
				break;
			case 'POST':
				$this->action='add';
				break;
			case 'PUT':
				$this->action='edit';
				break;
			case 'DELETE':
				$this->action='delete';
				break;
			default:
				$this->action='index';
				break;
		}
		// чистим массив GET от параметра controller
		unset($_GET['controller']);
		// оставшиеся параметры созраняем в массиве request
		$this->request=$_GET;
	}


	// запускаем приложение
	public function run(){
		// определяем название класса контроллера например (usersController)
		$controllerName=$this->controller.'Controller';
		
		// определяем название модели (например users)
		$modelName=$this->controller;
		
		/* 	создаем экземпляр контроллера
		 *	передаем в него название модели
		 */
		$controllerInstanse = new $controllerName($modelName);
		// копируем название действия в переменную
		$action=$this->action;
		// если метод существует - выполняем его, если не существует, возвращяем ошибку
		if(method_exists($controllerInstanse, $action)){
			// выполняем действие в контроллере
			$controllerInstanse->$action($this->request);
			// получаем результат
			$this->response=$controllerInstanse->getresponse();
		}else{
			$this->response=false;
		}
		
		if($this->controller != "index")
		{
            header('Content-Type: application/json');
            $this->response = json_encode($this->response);
		}
		// возвращяем результат
		return $this->response;
	}
}