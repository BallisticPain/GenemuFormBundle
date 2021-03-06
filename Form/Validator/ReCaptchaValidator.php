<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Form\Validator;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormValidatorInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

/**
 * ReCaptchaValidator
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class ReCaptchaValidator implements FormValidatorInterface
{
    protected $request;
    protected $privateKey;

    /**
     * Construct
     *
     * @param Request $request
     * @param string  $privateKey
     */
    public function __construct(Request $request, $privateKey)
    {
        $this->request = $request;
        $this->privateKey = $privateKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(FormInterface $form)
    {
        $request = $this->request->request;
        $server = $this->request->server;

        $parameters = array(
            'privatekey' => $this->privateKey,
            'challenge' => $request->get('recaptcha_challenge_field'),
            'response' => $request->get('recaptcha_response_field'),
            'remoteip' => $server->get('REMOTE_ADDR')
        );

        if (empty($parameters['challenge']) || empty($parameters['response'])) {
            $form->addError(new FormError('The captcha is not valid.'));
        }

        if (true !== ($answer = $this->check($parameters, $form->getAttribute('option_validator')))) {
            $form->addError(new FormError(sprintf('Unable to check the captcha from the server. (%s)', $answer)));
        }
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $parameters The value that should be validated
     * @param mixed $options    The option server
     *
     * @return Boolean Whether or not the value is valid
     */
    protected function check(array $parameters, array $options)
    {
        if (false === ($fs = @fsockopen($options['server_host'], $options['server_port'], $errno, $errstr, $options['server_timeout']))) {
            return $errstr;
        }

        $query = http_build_query($parameters, null, '&');

        fwrite($fs, sprintf(
            "POST %s HTTP/1.0\r\n".
            "Host: %s\r\n".
            "Content-Type: application/x-www-form-urlencoded\r\n".
            "Content-Length: %d\r\n".
            "User-Agent: reCAPTCHA/PHP/symfony\r\n".
            "\r\n%s", $options['server_path'], $options['server_host'], strlen($query), $query)
        );

        $response = '';
        while (!feof($fs)) {
            $response .= fgets($fs, 1160);
        }
        fclose($fs);

        $response = explode("\r\n\r\n", $response, 2);
        $answers = explode("\n", $response[1]);

        return 'true' == trim($answers[0])?true:$answers[1];
    }
}
