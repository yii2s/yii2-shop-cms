<?php

use app\backend\widgets\BackendWidget;
use kartik\helpers\Html;
use kartik\icons\Icon;
use app\backend\components\ActiveForm;
use vova07\imperavi\Widget as ImperaviWidget;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * @var $this \yii\web\View
 * @var $model \app\models\Category
 */
$this->title = Yii::t('app', 'Category edit');

$this->params['breadcrumbs'][] = ['url' => ['/backend/category/index'], 'label' => Yii::t('app', 'Categories')];
if (($model->parent_id > 0) && (null !== $parent = \app\models\Category::findById($model->parent_id, null, null))) {
    $this->params['breadcrumbs'][] = [
        'url' => [
            '/backend/category/index',
            'id' => $parent->id,
            'parent_id' => $parent->parent_id
        ],
        'label' => $parent->name
    ];
}
$this->params['breadcrumbs'][] = $this->title;

?>

<?=app\widgets\Alert::widget(
    [
        'id' => 'alert',
    ]
);?>

<?php $form = ActiveForm::begin(['id' => 'category-form', 'type' => ActiveForm::TYPE_HORIZONTAL]); ?>

<?php $this->beginBlock('submit'); ?>
<div class="form-group no-margin">
    <?php if (!$model->isNewRecord): ?>
        <?=Html::a(
            Icon::show('eye') . Yii::t('app', 'Preview'),
            [
                '/product/list',
                'category_id' => $model->id,
                'category_group_id' => $model->category_group_id,
            ],
            [
                'class' => 'btn btn-info',
                'target' => '_blank',
            ]
        )?>
    <?php endif; ?>
    <?=
    Html::a(
        Icon::show('arrow-circle-left') . Yii::t('app', 'Back'),
        Yii::$app->request->get('returnUrl', ['/backend/product/index']),
        ['class' => 'btn btn-danger']
    )
    ?>
    <?php if ($model->isNewRecord): ?>
        <?=Html::submitButton(
            Icon::show('save') . Yii::t('app', 'Save & Go next'),
            [
                'class' => 'btn btn-success',
                'name' => 'action',
                'value' => 'next',
            ]
        )?>
    <?php endif; ?>
    <?=Html::submitButton(
        Icon::show('save') . Yii::t('app', 'Save & Go back'),
        [
            'class' => 'btn btn-warning',
            'name' => 'action',
            'value' => 'back',
        ]
    );?>
    <?=
    Html::submitButton(
        Icon::show('save') . Yii::t('app', 'Save'),
        [
            'class' => 'btn btn-primary',
            'name' => 'action',
            'value' => 'save',
        ]
    )
    ?>
</div>
<?php $this->endBlock('submit'); ?>

<section id="widget-grid">
    <div class="row">
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

            <?php BackendWidget::begin(
                [
                    'title' => Yii::t('app', 'Category'),
                    'icon' => 'tree',
                    'footer' => $this->blocks['submit']
                ]
            ); ?>

            <?=$form->field($model, 'active')->widget(\kartik\switchinput\SwitchInput::className())?>

            <?php if ($model->parent_id == 0): ?>
                <?=$form->field($model, 'category_group_id')->dropDownList(
                        \app\components\Helper::getModelMap(\app\models\CategoryGroup::className(), 'id', 'name')
                    )?>
            <?php endif; ?>

            <?=$form->field($model, 'name')?>

            <?=
            $form->field(
                $model,
                'title',
                [
                    'copyFrom'=>[
                        "#category-name",
                        "#category-h1",
                        "#category-breadcrumbs_label",
                    ]
                ]
            )
            ?>

            <?=
            $form->field(app\models\ViewObject::getByModel($model, true), 'view_id')->dropDownList(
                    app\models\View::getAllAsArray()
                );
            ?>

            <?=$form->field($model, 'announce')->widget(
                ImperaviWidget::className(),
                [
                    'settings' => [
                        'replaceDivs' => false,
                        'minHeight' => 200,
                        'paragraphize' => true,
                        'pastePlainText' => true,
                        'buttonSource' => true,
                        'imageManagerJson' => Url::to(['/backend/dashboard/imperavi-images-get']),
                        'plugins' => [
                            'table',
                            'fontsize',
                            'fontfamily',
                            'fontcolor',
                            'video',
                            'imagemanager',
                        ],
                        'replaceStyles' => [],
                        'replaceTags' => [],
                        'deniedTags' => [],
                        'removeEmpty' => [],
                        'imageUpload' => Url::to(['/backend/dashboard/imperavi-image-upload']),
                    ],
                ]
            );?>

            <?=$form->field($model, 'content')->widget(
                ImperaviWidget::className(),
                [
                    'settings' => [
                        'replaceDivs' => false,
                        'minHeight' => 200,
                        'paragraphize' => true,
                        'pastePlainText' => true,
                        'buttonSource' => true,
                        'imageManagerJson' => Url::to(['/backend/dashboard/imperavi-images-get']),
                        'plugins' => [
                            'table',
                            'fontsize',
                            'fontfamily',
                            'fontcolor',
                            'video',
                            'imagemanager',
                        ],
                        'replaceStyles' => [],
                        'replaceTags' => [],
                        'deniedTags' => [],
                        'removeEmpty' => [],
                        'imageUpload' => Url::to(['/backend/dashboard/imperavi-image-upload']),
                    ],
                ]
            );?>

            <?=$form->field($model, 'sort_order');?>
            <?=
            $form->field($model, 'parent_id')
                ->dropDownList(
                    ArrayHelper::map(
                        \app\models\Category::find()
                            ->where('id != :id', ['id'=>$model->id])
                            ->all(),
                        'id',
                        'name'
                    ));
            ?>
            <?php BackendWidget::end(); ?>

            <?php
            BackendWidget::begin(
                [
                    'title' => Yii::t('app', 'Images'),
                    'icon' => 'image',
                    'footer' => $this->blocks['submit']
                ]
            ); ?>

            <div id="actions">
                <?=
                \yii\helpers\Html::tag(
                    'span',
                    Icon::show('plus') . Yii::t('app', 'Add files..'),
                    [
                        'class' => 'btn btn-success fileinput-button'
                    ]
                )?>
            </div>

            <?=\app\widgets\image\ImageDropzone::widget(
                [
                    'name' => 'file',
                    'url' => ['/backend/product/upload'],
                    'removeUrl' => ['/backend/product/remove'],
                    'uploadDir' => '/theme/resources/product-images',
                    'sortable' => true,
                    'sortableOptions' => [
                        'items' => '.dz-image-preview',
                    ],
                    'objectId' => $object->id,
                    'modelId' => $model->id,
                    'htmlOptions' => [
                        'class' => 'table table-striped files',
                        'id' => 'previews',
                    ],
                    'options' => [
                        'clickable' => ".fileinput-button",
                    ],
                ]
            );?>

            <?php BackendWidget::end(); ?>
        </article>

        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <?php BackendWidget::begin(
                [
                    'title' => Yii::t('app', 'SEO'),
                    'icon' => 'cogs',
                    'footer' => $this->blocks['submit']
                ]
            ); ?>

            <?=
            $form->field(
                $model,
                'slug',
                [
                    'makeSlug' => [
                        "#category-name",
                        "#category-title",
                        "#category-h1",
                        "#category-breadcrumbs_label",
                    ]
                ]
            )
            ?>

            <?=
            $form->field(
                $model,
                'h1',
                [
                    'copyFrom' => [
                        "#category-name",
                        "#category-title",
                        "#category-breadcrumbs_label",
                    ]
                ]
            )
            ?>

            <?=
            $form->field(
                $model,
                'breadcrumbs_label',
                [
                    'copyFrom' => [
                        "#category-name",
                        "#category-title",
                        "#category-h1",
                    ]
                ]
            )
            ?>

            <?=$form->field($model, 'meta_description')->textarea()?>

            <?=$form->field($model, 'title_append')?>

            <?php BackendWidget::end(); ?>

            <?=
            \app\properties\PropertiesWidget::widget(
                [
                    'model' => $model,
                    'form' => $form,
                ]
            );
            ?>

        </article>
    </div>
</section>

<?php ActiveForm::end(); ?>
