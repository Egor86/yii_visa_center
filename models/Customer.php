<?php

namespace app\models;

use Yii;
use yii\helpers\BaseUrl;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "customer".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $passport
 * @property string $citizenship
 * @property string $password_hash
 * @property string $password
 * @property string $confirm_password
 * @property string $access_token
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Customer extends \yii\db\ActiveRecord implements IdentityInterface
{
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_APPLICATION_FORM = 'app_form';
    const SCENARIO_VERIFIED = 'verified';

    // statuses
    const STATUS_VERIFY = 1;
    const STATUS_NOT_VERIFY = 2;

    public $password;
    public $confirm_password;

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)) {
            if ($insert) {
                $this->generateToken();
                $this->setPassword($this->password);
                $this->status = self::STATUS_NOT_VERIFY;
            }
        }

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert && $this->status === self::STATUS_NOT_VERIFY) {
            \Yii::$app->mailer
                ->compose('visa_confirm', ['confirmLink' => $this->generateConfirmLink()])
                ->setTo($this->email)
                ->send();
        }
    }

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
            [['email', 'password', 'confirm_password'], 'required'],
            ['email', 'email'],
            [['password', 'access_token', 'confirm_password'], 'string'],
            [['password_hash'], 'string', 'max' => 60],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['first_name', 'last_name', 'email', 'passport', 'citizenship'], 'string', 'max' => 255],
            [['email'], 'unique'],
            ['password', 'string', 'min' => 6],
            ['confirm_password', 'comparePassword'],
            ['status', 'default', 'value' => self::STATUS_NOT_VERIFY],
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
            self::SCENARIO_VERIFIED => ['status']
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

    private function generateToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    private function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    private function generateConfirmLink()
    {
        return Url::to(['customer/confirm',
            'security_key' => $this->access_token,
            'data' => $this->getAuthKey()
        ], true);
    }

    public static function findIdentity($id)
    {
        $customer = self::findOne($id);
        return $customer->id ? $customer : null;
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return Customer::findOne(['access_token' => $token]);
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return md5($this->id . $this->email);
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() == $authKey;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return self::findOne(['email' => $email]);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
}
