<?php

namespace templatemonster\ldapauth\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use templatemonster\ldapauth\models\Acl;
use templatemonster\ldapauth\models\AclForm;
use templatemonster\ldapauth\models\search\AclSearch;

/**
 * AclController implements the CRUD actions for Acl model.
 */
class AclController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Acl models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AclSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Acl model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AclForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
            'roles' => ArrayHelper::map(Yii::$app->getAuthManager()->getRoles(), 'name', 'name')
        ]);
    }

    /**
     * Updates an existing Acl model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = new AclForm();
        $model->setModel($this->findModel($id));
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->render('update', [
            'model' => $model,
            'roles' => ArrayHelper::map(Yii::$app->ldapGroupsManager->getRoles(), 'name', 'name')
        ]);
    }

    /**
     * Deletes an existing Acl model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Yii::$app->ldapGroupsManager->revokeAll($id);
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Acl the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Acl::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
