<?php
declare(strict_types=1);

/**
 * Users Table
 *
 * PHP version 5.3
 *
 * @category Table
 * @package  Lil
 * @author   Arhim d.o.o. <info@arhim.si>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.arhint.si
 */
namespace Lil\Model\Table;

use ArrayObject;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Email\Email;
use Cake\ORM\Query;
use Cake\ORM\Rule\IsUnique;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
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
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->setTable(Configure::read('Lil.usersTable'));

        $this->addBehavior('Timestamp');
    }

    /**
     * Costum Auth finder method
     *
     * @param array $query Table Configuration.
     * @param array $options Options array.
     * @return \Cake\ORM\Query
     */
    public function findAuth(Query $query, array $options): Query
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
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $user_fields = Configure::read('Lil.authFields');

        $validator
            ->add('id', 'valid', ['rule' => 'uuid'])
            ->allowEmptyString('id', 'create')
            ->notEmptyString('title')
            ->notEmptyString($user_fields['username']);

        return $validator;
    }

    /**
     * Registration validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationRegistration(Validator $validator): Validator
    {
        $user_fields = Configure::read('Lil.authFields');

        $validator = new Validator();
        $validator
            ->add('id', 'valid', ['rule' => 'uuid'])
            ->allowEmptyString('id', 'create')
            //->notEmptyString(Configure::read('Lil.userDisplayField'))
            ->notEmptyString($user_fields['username'])
            ->notEmptyString($user_fields['password'])
            ->requirePresence('repeat_pass')
            ->notEmptyString('repeat_pass')
            ->add(
                'repeat_pass',
                'match',
                [
                    'rule' => function ($value, $context) {
                        $user_fields = Configure::read('Lil.authFields');

                        return $value == $context['data'][$user_fields['password']];
                    },
                ],
            );

        return $validator;
    }

    /**
     * Properties validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationProperties(Validator $validator): Validator
    {
        $user_fields = Configure::read('Lil.authFields');

        $validator = new Validator();
        $validator
            ->requirePresence('id')
            ->notEmptyString('id')
            ->add('id', 'valid', ['rule' => 'uuid'])

            ->notEmptyString(Configure::read('Lil.userDisplayField'))

            ->allowEmptyString(
                'old_pass',
                function ($context) {
                    $user_fields = Configure::read('Lil.authFields');

                    return empty($context['data'][$user_fields['password']]);
                },
            )
            // require old_pass only when user wants to change password
            ->requirePresence(
                'old_pass',
                function ($context) {
                    $user_fields = Configure::read('Lil.authFields');

                    return !empty($context['data'][$user_fields['password']]);
                },
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

                    return (new DefaultPasswordHasher())->check(
                        $value,
                        $user->{$user_fields['password']},
                    );
                },
                ],
            )

            ->allowEmptyString($user_fields['password'])
            ->add(
                $user_fields['password'],
                'minLength',
                [
                'rule' => ['minLength', 6],
                ],
            )

            ->allowEmptyString(
                'repeat_pass',
                function ($context) {
                    $user_fields = Configure::read('Lil.authFields');

                    return empty($context['data'][$user_fields['password']]);
                },
            )
            // require repeat_pass only when user wants to change password
            ->requirePresence(
                'repeat_pass',
                function ($context) {
                    $user_fields = Configure::read('Lil.authFields');

                    return !empty($context['data'][$user_fields['password']]);
                },
            )

            // repeat password should match new password
            ->add(
                'repeat_pass',
                'match',
                [
                    'rule' => function ($value, $context) {
                        $user_fields = Configure::read('Lil.authFields');

                        return $value == $context['data'][$user_fields['password']];
                    },
                ],
            );

        return $validator;
    }

    /**
     * validationResetPassword validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationResetPassword(Validator $validator): Validator
    {
        $user_fields = Configure::read('Lil.authFields');
        $validator = new Validator();
        $validator
            ->add(
                $user_fields['password'],
                'minLength',
                [
                'rule' => ['minLength', 4],
                ],
            )
            ->requirePresence(
                'repeat_pass',
                function ($context) {
                    $user_fields = Configure::read('Lil.authFields');

                    return !empty($context['data'][$user_fields['password']]);
                },
            )
            ->allowEmptyString('repeat_pass')

            // repeat password should match new password
            ->add(
                'repeat_pass',
                'match',
                [
                    'rule' => function ($value, $context) {
                        $user_fields = Configure::read('Lil.authFields');

                        return $value == $context['data'][$user_fields['password']];
                    },
                ],
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

                    return (new DefaultPasswordHasher())->check(
                        $value,
                        $user->{$user_fields['password']},
                    );
                },
                ],
            );

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $user_fields = Configure::read('Lil.authFields');
        $rules->add(
            new IsUnique([$user_fields['username']]),
            'unique',
            ['errorField' => $user_fields['username']],
        );

        return $rules;
    }

    /**
     * Sends reset email
     *
     * @param \Lil\Model\Table\entity $user User entity.
     * @return bool
     */
    public function sendResetEmail(entity $user): bool
    {
        $reset_key = uniqid();
        $user->{Configure::read('Lil.passwordResetField')} = $reset_key;
        if ($this->save($user)) {
            $email = new Email('default');
            $email->from(
                [Configure::read('Lil.from.email')
                => Configure::read('Lil.from.name'),
                ],
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

    /**
     * Hashes password
     *
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\Datasource\EntityInterface $entity Entity object
     * @param \ArrayObject $options Options array
     * @return bool
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options): bool
    {
        $passwordField = Configure::read('Lil.authFields.password');
        if ($entity->isDirty($passwordField)) {
            $entity->{$passwordField} = (new DefaultPasswordHasher())->hash($entity->{$passwordField});
        }

        return true;
    }
}
