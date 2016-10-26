<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model templatemonster\ldapauth\models\Acl */
/* @var $roles yii\rbac\Role[] */

$this->title = Yii::t('backend', 'Update {modelClass}: ', ['modelClass' => 'Acl']) . ' ' . $model->ldap_group;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Acl'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ldap_group, 'url' => ['', 'id' => $model->ldap_group]];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('backend', 'Update')];
?>
<div class="user-update">

    <?php echo $this->render('_form', [
        'model' => $model,
        'roles' => $roles
    ]) ?>

</div>
