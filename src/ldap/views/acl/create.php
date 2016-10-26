<?php
/* @var $this yii\web\View */
/* @var $model templatemonster\ldapauth\models\AclForm */
/* @var $roles yii\rbac\Role[] */
$this->title = Yii::t('backend', 'Create {modelClass}', [
    'modelClass' => 'Acl',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Acl'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <?php echo $this->render('_form', [
        'model' => $model,
        'roles' => $roles
    ]) ?>

</div>
