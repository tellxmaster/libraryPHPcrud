<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\Libro;
use app\models\LibroSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\data\Pagination;

/**
 * LibroController implements the CRUD actions for Libro model.
 */
class LibroController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access'=>[
                    'class'=>AccessControl::className(),
                    'only'=>['index','view','create','update','delete'],
                    'rules'=>[
                        [
                            'allow'=>true,
                            'roles'=>['@']
                        ]
                    ]
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Libro models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LibroSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Libro model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Libro model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Libro();

        $this->uploadFile($model);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Libro model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $this->uploadFile($model);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Libro model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if(file_exists($model->imagen)){
            unlink($model->imagen);
        }
        
        $model->delete();

        return $this->redirect(['index']);
    }

    public function actionLista(){
        
        $model = Libro::find();
        $paginacion = new Pagination([
            'defaultPageSize'=>3,
            'totalCount'=>$model->count()
        ]);
        $libros = $model->orderBy('titulo')->offset($paginacion->offset)->limit($paginacion->limit)->all();
        return $this->render('lista',['libros'=>$libros,'paginacion'=>$paginacion]);

    }

    /**
     * Finds the Libro model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Libro the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Libro::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function uploadFile(Libro $model){
        if($model->load(Yii::$app->request->post()) ){
            $model->archivo = UploadedFile::getInstance($model,'archivo');

                // ERROR VALIDATE
                if($model->archivo){

                    if(file_exists($model->imagen)){
                        unlink($model->imagen);
                    }

                    $rutaArchivo='uploads/'.time()."_".$model->archivo->baseName.".".$model->archivo->extension;
                    if ($model->archivo->saveAs($rutaArchivo)) {
                        $model->imagen=$rutaArchivo;
                    }
                }
                
                if ($model->save(false)) {
                    return $this->redirect(['index']);
                }
            
        }
    }
}
