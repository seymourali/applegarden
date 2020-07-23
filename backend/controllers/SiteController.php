<?php
namespace backend\controllers;

use backend\models\Apple;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
	                [
		                'allow' => true,
		                'roles' => ['@'],
	                ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
	public function actionIndex() {
		$apple_model = new Apple();
		$apples_on_tree = $apple_model->on_tree();
		$apples_fell = $apple_model->fell();
		if (Yii::$app->request->post('generate')) {
			$apple_model->generate();
			$this->redirect(Yii::$app->request->referrer);
		}
		return $this->render('index', [
			'apples_on_tree' => $apples_on_tree,
			'apples_fell' => $apples_fell
		]);
	}
    
	public function actionFallAppleToGround() {
		if (!Yii::$app->request->isAjax) {
			Yii::$app->end();
		}
		Yii::$app->response->format = 'json';
		$response = ['message' => 'apple_not_fell', 'success' => false];
		$apple_id = strip_tags((int)Yii::$app->request->get('id'));
		if (empty($apple_id)) {return false;}
		$apple_model = new Apple();
		$fell = $apple_model->fallToGround($apple_id);
		if ($fell) {
			$response['success'] = true;
			$response['message'] = 'apple_successfully_fell';
			$apples_fell = $apple_model->fell();
			$response['data'] = ['fell' => $apples_fell];
		}
		return $response;
	}
	
	public function actionEatApple() {
		if (!Yii::$app->request->isAjax) {
			Yii::$app->end();
		}
		Yii::$app->response->format = 'json';
		$response = ['message' => 'apple_not_eaten', 'success' => false];
		$post_data = Yii::$app->request->post();
		$apple_id = strip_tags((int)$post_data['id']);
		$eaten_percent = strip_tags($post_data['percent']);
		$apple_model = new Apple();
		$eaten = $apple_model->eat($apple_id, $eaten_percent);
		if ($eaten) {
			$apple = $apple_model->get($apple_id);
			if (empty($apple)) {
				$response = [
					'success' => true,
					'message' => 'apple_was_eaten_completely_successfully',
				];
			} else {
				$response = [
					'success' => true,
					'message' => 'apple_was_eaten_partially_successfully',
					'data' => $apple['size']
				];
			}
		}
		return $response;
	}

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
