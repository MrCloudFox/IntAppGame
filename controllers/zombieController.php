<?php

class zombieController extends Controller
{
    public function index()
    {
        $zombies = $this->model->load();
        $this->setResponse($zombies);
    }

    public function view($data)
    {
        $zombie = $this->model->load($data['id']);
        $this->setResponse($zombie);
    }

    public function add()
    {
        $postData=json_decode(file_get_contents('php://input'), TRUE);

        if(isset($postData['id']) && isset($postData['name']) && isset($postData['image'])
            && isset($postData['power']) && isset($postData['speed'])){

            $dataToSave = array(
                'id' => $postData['id'],
                'name' => $postData['name'],
                'image' => $postData['image'],
                'power' => $postData['power'],
                'speed' => $postData['speed']);

            $addedItem=$this->model->create($dataToSave);
            $this->setResponse($addedItem);
        }
    }

    public function edit($data)
    {
        $postData=json_decode(file_get_contents('php://input'), TRUE);

        if(isset($postData['id']) && isset($postData['name']) && isset($postData['image'])
            && isset($postData['power']) && isset($postData['speed'])){

            $dataToSave = array(
                'id' => $postData['id'],
                'name' => $postData['name'],
                'image' => $postData['image'],
                'power' => $postData['power'],
                'speed' => $postData['speed']);

            $updateItem = $this->model->save($data['id'], $dataToSave);
            $this->setResponse($updateItem);
        }
    }

    public function delete($data)
    {
        $deleteItem = $this->model->delete($data['id']);
        $this->setResponse($deleteItem);
    }
}