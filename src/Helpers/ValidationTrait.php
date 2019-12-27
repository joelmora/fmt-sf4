<?php
namespace isoft\fmtsf4\Helpers;

use App\Exceptions\ValidationException;
use Symfony\Component\Validator\Validation;

trait ValidationTrait
{
    /**
     * @var TraceableValidator
     */
    protected $validator;

    /**
     * Validate based on constraints and return an array of errors and 422 if fails
     * @param $data
     * @param $validationConstraints
     * @return mixed
     * @throws ValidationException
     */
    public function validateData($data, $validationConstraints, $validator = null)
    {
        if (get_class($this) != "App\Service\SecurityService") {
            $this->validator = Validation::createValidator();
        } else {
            $this->validator = $validator;
        }

        $this->validateEmpty($data);

        $violations = $this->validator->validate($data, $validationConstraints);
        $errors = [];

        if ($violations->count()) {
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }

            throw new ValidationException($errors);
        }
    }

    //Validate whether the input is empty or not
    public function validateEmpty($data)
    {
        if (empty($data)) {
            throw new ValidationException('Empty JSON');
        }
    }
}
