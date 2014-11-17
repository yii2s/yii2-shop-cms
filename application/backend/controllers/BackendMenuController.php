<?php

namespace app\backend\controllers;

use app\backend\actions\JSTreeGetTrees;
use app\backend\models\BackendMenu;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class BackendMenuController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['setting manage'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'getTree' => [
                'class' => JSTreeGetTrees::className(),
                'modelName' => BackendMenu::className(),
                'label_attribute' => 'name',
                'vary_by_type_attribute' => null,
            ],
        ];
    }

    public function actionIndex($parent_id = 1)
    {
        $searchModel = new BackendMenu();
        $searchModel->parent_id = $parent_id;

        $params = Yii::$app->request->get();

        $dataProvider = $searchModel->search($params);

        $model = null;
        if ($parent_id > 0) {
            $model = BackendMenu::findOne($parent_id);
        }

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'model' => $model,
            ]
        );
    }

    public function actionEdit($parent_id = null, $id = null)
    {
        if (null === $parent_id) {
            throw new NotFoundHttpException;
        }

        /** @var null|BackendMenu|HasProperties $model */
        $model = null;
        if (null !== $id) {
            $model = BackendMenu::findById($id);
        } else {
            if (null !== $parent = BackendMenu::findById($parent_id)) {
                $model = new BackendMenu;
                $model->loadDefaultValues();
                $model->parent_id = $parent_id;

            } else {
                $model = new BackendMenu;
                $model->loadDefaultValues();
                $model->parent_id = 0;
            }
        }

        if (null === $model) {
            throw new ServerErrorHttpException;
        }

        $post = \Yii::$app->request->post();
        if ($model->load($post) && $model->validate()) {

            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Record has been saved'));
                return $this->redirect(
                    [
                        '/backend/backend-menu/index',
                        // 'id' => $model->id,
                        'parent_id' => $model->parent_id
                    ]
                );
            } else {
                throw new ServerErrorHttpException;
            }
        }

        return $this->render(
            'form',
            [
                'model' => $model,
            ]
        );
    }

    public function actionTest()
    {
        echo "<PRE>";
        return \yii\helpers\VarDumper::dump(\app\backend\models\BackendMenu::getAllMenu());
    }

    public function actionDelete($id = null, $parent_id = null)
    {

        if ((null === $id) || (null === $model = BackendMenu::findById($id))) {
            throw new NotFoundHttpException;
        }

        if (!$model->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('shop', 'The object is placed in the cart'));
        } else {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Object has been removed'));
        }

        return $this->redirect(Url::to(['index', 'parent_id' => $model->parent_id]));
    }

}
