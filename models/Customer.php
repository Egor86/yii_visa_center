<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $passport
 * @property string $citizenship
 * @property string $password
 * @property string $confirm_password
 * @property string $access_token
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Customer extends \yii\db\ActiveRecord
{
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_APPLICATION_FORM = 'app_form';

    // statuses
    const STATUS_VERIFY = 1;
    const STATUS_NOT_VERIFY = 2;
    public $confirm_password;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            [['password', 'access_token', 'confirm_password'], 'string'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['first_name', 'last_name', 'email', 'passport', 'citizenship'], 'string', 'max' => 255],
            [['email'], 'unique'],
            ['confirm_password', 'comparePassword']
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'email' => Yii::t('app', 'Email'),
            'passport' => Yii::t('app', 'Passport'),
            'citizenship' => Yii::t('app', 'Citizenship'),
            'password' => Yii::t('app', 'Password'),
            'access_token' => Yii::t('app', 'Access Token'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_REGISTER => ['email', 'password', 'confirm_password'],
            self::SCENARIO_LOGIN => ['email', 'password'],
            self::SCENARIO_APPLICATION_FORM => ['first_name', 'last_name', 'passport', 'citizenship'],
        ];
    }

    public function comparePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->{$attribute} !== $this->password ?
                $this->addError($attribute, Yii::t('app/error', 'Password confirmation is not equal to password.')) :
                true;
        }
    }

    public static function findIdentity($id)
    {
        $customer = self::findOne($id);
        return $customer->getId() ? $customer : null;
    }
}
