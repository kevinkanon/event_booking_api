<?php

namespace App\Service;

class PDOStatement {

    /**
     *
     * @var \PDOStatement
     */
    protected $decorated;
    /**
     *
     * @var string
     */
    protected $statement;

    /**
     *
     * @var array
     */
    protected $boundArrayValues = [];

    /**
     *
     * @var PDO
     */
    protected $pdo;

    public function __construct($statement, PDO $pdo)
    {
        $this->statement = $statement;
        $this->pdo = $pdo;
    }

    public function __call($name , array $arguments)
    {
        if ($this->decorated === null) {
            $this->preparePDOStatement();
        }

        return call_user_func_array(array($this->decorated, $name), $arguments);
    }


    public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR) {
        if($this->pdo->isArrayType($data_type)) {
            if(substr($parameter, 0, 1) !== ':') {
                throw new Exception('This PDO implementation only supports named placeholders as ARRAY parameters.');
            }
            if($this->decorated !== null) {
                //
                throw new \LogicException('Array parameters must be bound before any other operation.');
            }

            $this->boundArrayValues[$parameter] = [$value, $data_type];

        } else {
            if($this->decorated === null) {
                $this->preparePDOStatement();
            }
            return $this->decorated->bindValue($parameter, $value, $data_type);
        }
    }

    protected function preparePDOStatement() {

        $this->prepareBoundValues();
        $this->expandNamedParameters();

        $this->decorated = $this->pdo->nativePrepare($this->statement);

        $this->bindArrayValues();

    }

    private function prepareBoundValues() {
        foreach($this->boundArrayValues as $parameterName => &$values) {
            $valuesArray = &$values[0];
            $valuesType = $values[1];

            $explodedValues = [];
            foreach($valuesArray as $oneValue) {
                $explodedValues[$parameterName . uniqid()] = $oneValue;
            }
            $valuesArray = $explodedValues;
        }
    }

    private function expandNamedParameters() {
        foreach($this->boundArrayValues as $arrayParameterName => $arrayParameterData) {
            $finalParameterNames = array_keys($arrayParameterData[0]);

            $z = preg_quote($arrayParameterName);
            $this->statement = preg_replace(
                '/(' . preg_quote($arrayParameterName) . ')\b/',
                implode(',', $finalParameterNames),
                $this->statement
            );
        }
    }

    private function bindArrayValues() {
        foreach($this->boundArrayValues as $arrayParameterData) {
            $values = $arrayParameterData[0];
            $type = $this->pdo->arrayTypeToNativeType($arrayParameterData[1]);

            foreach($values as $finalParamName => $paramValue) {
                $this->decorated->bindValue($finalParamName, $paramValue, $type);
            }

        }
    }

}