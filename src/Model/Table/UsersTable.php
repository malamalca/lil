<?php
/**
 * Users Table
 *
 * @category Table
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
namespace Lil\Model\Table;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Email\Email;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Rule\IsUnique;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Cake\Validation\Validator;

/**
 * Users Table
 *
 * This table manages users.
 *
 * @category Table
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
class UsersTable extends Table
{
    /**
     * Initialize method.
     *
     * @param array $config Table Configuration.
     *
     * @return void
     */
    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');

        $this->hasMany('Settings', [
            'foreignKey' => 'owner_id',
        ]);
    }

    /**
     * Costum Auth finder method
     *
     * @param \Cake\ORM\Query $query Query object.
     * @param array $options Query options
     *
     * @return \Cake\ORM\Query
     */
    public function findAuth(\Cake\ORM\Query $query, array $options)
    {
        $event = new Event('Lil.authFinder', $this, ['query' => $query, 'options' => $options]);
        EventManager::instance()->dispatch($event);

        if (!empty($event->result)) {
            $query = $event->result;
        }

        return $query;
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $user_fields = Configure::read('Lil.authFields');

        $validator
            ->add('id', 'valid', ['rule' => 'uuid'])
            ->allowEmpty('id', 'create')
            ->notEmpty('title')
            ->notEmpty($user_fields['username']);

        return $validator;
    }

    /**
     * Registration validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationRegistration($validator)
    {
        $user_fields = Configure::read('Lil.authFields');

        $validator = new Validator();
        $validator
            ->add('id', 'valid', ['rule' => 'uuid'])
            ->allowEmpty('id', 'create')
            //->notEmpty(Configure::read('Lil.userDisplayField'))
            ->notEmpty($user_fields['username'])
            ->notEmpty($user_fields['password'])
            ->requirePresence('repeat_pass')
            ->notEmpty('repeat_pass')
            ->add(
                'repeat_pass',
                'match',
                [
                    'rule' => function ($value, $context) {
                        $user_fields = Configure::read('Lil.authFields');

                        return $value == $context['data'][$user_fields['password']];
                    }
                ]
            );

        return $validator;
    }

    /**
     * Properties validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationProperties($validator)
    {
        $user_fields = Configure::read('Lil.authFields');

        $validator = new Validator();
        $validator
            ->requirePresence('id')
            ->notEmpty('id')
            ->add('id', 'valid', ['rule' => 'uuid'])

            ->notEmpty(Configure::read('Lil.userDisplayField'))

            ->allowEmpty(
                'old_pass',
                function ($context) {
                    $user_fields = Configure::read('Lil.authFields');

                    return empty($context['data'][$user_fields['password']]);
                }
            )
            // require old_pass only when user wants to change password
            ->requirePresence(
                'old_pass',
                function ($context) {
                    $user_fields = Configure::read('Lil.authFields');

                    return !empty($context['data'][$user_fields['password']]);
                }
            )
            ->add(
                'old_pass',
                'match',
                [
                'rule' => function ($value, $context) {
                    $user_fields = Configure::read('Lil.authFields');
                    $Users = TableRegistry::get('Lil.Users');
                    $user = $Users->find()
                        ->select()
                        ->where(['id' => $context['data']['id']])
                        ->first();

                    return (new DefaultPasswordHasher)->check(
                        $value,
                        $user->{$user_fields['password']}
                    );
                }
                ]
            )

            ->allowEmpty($user_fields['password'])
            ->add(
                $user_fields['password'],
                'minLength',
                [
                'rule' => ['minLength', 6]
                ]
            )

            ->allowEmpty(
                'repeat_pass',
                function ($context) {
                    $user_fields = Configure::read('Lil.authFields');

                    return empty($context['data'][$user_fields['password']]);
                }
            )
            // require repeat_pass only when user wants to change password
            ->requirePresence(
                'repeat_pass',
                function ($context) {
                    $user_fields = Configure::read('Lil.authFields');

                    return !empty($context['data'][$user_fields['password']]);
                }
            )

            // repeat password should match new password
            ->add(
                'repeat_pass',
                'match',
                [
                    'rule' => function ($value, $context) {
                        $user_fields = Configure::read('Lil.authFields');

                        return $value == $context['data'][$user_fields['password']];
                    }
                ]
            );

        return $validator;
    }

    /**
     * validationResetPassword validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationResetPassword($validator)
    {
        $user_fields = Configure::read('Lil.authFields');
        $validator = new Validator();
        $validator
            ->add(
                $user_fields['password'],
                'minLength',
                [
                'rule' => ['minLength', 4]
                ]
            )
            ->requirePresence(
                'repeat_pass',
                function ($context) {
                    $user_fields = Configure::read('Lil.authFields');

                    return !empty($context['data'][$user_fields['password']]);
                }
            )
            ->allowEmpty('repeat_pass')

            // repeat password should match new password
            ->add(
                'repeat_pass',
                'match',
                [
                    'rule' => function ($value, $context) {
                        $user_fields = Configure::read('Lil.authFields');

                        return $value == $context['data'][$user_fields['password']];
                    }
                ]
            )

            ->add(
                'old_pass',
                'match',
                [
                'rule' => function ($value, $context) {
                    $user_fields = Configure::read('Lil.authFields');
                    $Users = TableRegistry::get('Lil.Users');
                    $user = $Users->find()
                        ->select()
                        ->where(['id' => $context['data']['id']])
                        ->first();

                    return (new DefaultPasswordHasher)->check(
                        $value,
                        $user->{$user_fields['password']}
                    );
                }
                ]
            );

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     *
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $user_fields = Configure::read('Lil.authFields');
        $rules->add(
            new IsUnique([$user_fields['username']]),
            'unique',
            ['errorField' => $user_fields['username']]
        );

        return $rules;
    }

    /**
     * Sends reset email
     *
     * @param entity $user User entity.
     *
     * @return void
     */
    public function sendResetEmail($user)
    {
        $reset_key = uniqid();
        $user->{Configure::read('Lil.passwordResetField')} = $reset_key;
        if ($this->save($user)) {
            $email = new Email('default');
            $email->from(
                [Configure::read('Lil.from.email')
                => Configure::read('Lil.from.name')
                ]
            );
            $email->to($user->{Configure::read('Lil.userEmailField')});
            $email->subject(__d('lil', 'Password Reset'));

            $email->template('Lil.reset');
            $email->emailFormat('text');
            $email->viewVars(['reset_key' => $reset_key]);
            $email->helpers(['Html']);

            return $email->send();
        }

        return false;
    }
}
