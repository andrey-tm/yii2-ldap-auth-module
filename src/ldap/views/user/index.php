<?php

use yii\grid\GridView;
use common\grid\EnumColumn;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Support Levels');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'username',
            'email:email',
            [
                'class' => EnumColumn::className(),
                'attribute' => 'status',
                'enum' => User::statuses(),
                'filter' => User::statuses()
            ],
            'created_at:datetime',
            'logged_at:datetime',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}' // disable delete/update actions
            ],
        ],
    ]); ?>
</div>
