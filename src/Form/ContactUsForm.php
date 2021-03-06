<?php
namespace ContactUs\Form;

use Zend\Filter;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Validator;

class ContactUsForm extends Form
{
    public function init()
    {
        $question = $this->getOption('question');
        $checkAnswer = $this->getOption('checkAnswer');
        $isAuthenticated = $this->getOption('isAuthenticated');

        $this->setAttribute('class', 'contact-form');

        // "From" is used instead of "email" to avoid some basic spammers.
        $this
            ->add([
                'name' => 'from',
                'type' => Element\Email::class,
                'options' => [
                    'label' => 'Email', // @translate
                ],
                'attributes' => [
                    'id' => 'from',
                    'required' => !$isAuthenticated,
                ],
            ])
            ->add([
                'name' => 'name',
                'type' => Element\Text::class,
                'options' => [
                    'label' => 'Name', // @translate
                ],
                'attributes' => [
                    'id' => 'name',
                    'required' => false,
                ],
            ])
            ->add([
                'name' => 'subject',
                'type' => Element\Text::class,
                'options' => [
                    'label' => 'Subject', // @translate
                ],
                'attributes' => [
                    'id' => 'subject',
                    'required' => false,
                ],
            ])
            ->add([
                'name' => 'message',
                'type' => Element\Textarea::class,
                'options' => [
                    'label' => 'Message', // @translate
                ],
                'attributes' => [
                    'id' => 'message',
                    'required' => true,
                ],
            ]);

        if ($question) {
            $this
                ->add([
                    'name' => 'answer',
                    'type' => Element\Text::class,
                    'options' => [
                        'label' => $question,
                    ],
                    'attributes' => [
                        'id' => 'answer',
                        'required' => true,
                    ],
                ])
                ->add([
                    'name' => 'check',
                    'type' => Element\Hidden::class,
                    'attributes' => [
                        'value' => substr(md5($question), 0, 16),
                    ],
                ]);
        }

        $this
            ->add([
                'name' => 'submit',
                'type' => Element\Submit::class,
                'attributes' => [
                    'id' => 'submit',
                    'value' => 'Send message', // @translate
                ],
            ]);

        $inputFilter = $this->getInputFilter();
        $inputFilter
            ->add([
                'name' => 'from',
                'required' => !$isAuthenticated,
            ])
            ->add([
                'name' => 'message',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StringTrim::class],
                ],
            ])
        ;
        if ($question) {
            $inputFilter->add([
                'name' => 'answer',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StringTrim::class],
                ],
                'validators' => [
                    [
                        'name' => Validator\Callback::class,
                        'options' => [
                            'callback' => function ($answer) use ($checkAnswer) {
                                return $answer === $checkAnswer;
                            },
                            'callbackOptions' => [
                                'checkAnswer' => $checkAnswer,
                            ],
                        ],
                    ],
                ],
            ]);
        }
    }
}
