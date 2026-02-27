<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Acl\Business\Model;

use Generated\Shared\Transfer\RulesTransfer;
use Generated\Shared\Transfer\RuleTransfer;
use Spryker\Shared\Acl\AclConstants;

class RuleValidator implements RuleValidatorInterface
{
    /**
     * @var array<\Generated\Shared\Transfer\RuleTransfer>
     */
    protected $allowedRules = [];

    /**
     * @var array<\Generated\Shared\Transfer\RuleTransfer>
     */
    protected $deniedRules = [];

    protected array $allowedCompiled = [];

    protected array $deniedCompiled = [];

    /**
     * @param array $rules
     * @param bool $condition
     *
     * @return bool
     */
    protected function validate(array $rules, $condition)
    {
        $count = 0;
        $total = count($rules);
        foreach ($rules as $value) {
            if ($value === $condition) {
                $count++;
            }
        }

        return $count === $total;
    }

    /**
     * @param array $rules
     * @param bool $condition
     *
     * @return array
     */
    protected function reset(array $rules, $condition)
    {
        $result = [];

        foreach ($rules as $key => $value) {
            $result[$key] = $condition;
        }

        return $result;
    }

    /**
     * @param \Generated\Shared\Transfer\RulesTransfer $rules
     *
     * @return $this
     */
    public function setRules(RulesTransfer $rules)
    {
        foreach ($rules->getRules() as $rule) {
            if ($rule->getType() === 'allow') {
                $this->addAllowedRule($rule);
            }
        }

        foreach ($rules->getRules() as $rule) {
            if ($rule->getType() === 'deny') {
                $this->addDeniedRule($rule);
            }
        }

        return $this;
    }

    /**
     * @param \Generated\Shared\Transfer\RuleTransfer $rule
     *
     * @return void
     */
    public function addRule(RuleTransfer $rule)
    {
        switch ($rule->getType()) {
            case 'allow':
                $this->addAllowedRule($rule);

                break;
            case 'deny':
                $this->addDeniedRule($rule);

                break;
        }
    }

    protected function addAllowedRule(RuleTransfer $rule): void
    {
        $this->allowedRules[] = $rule;
        $this->allowedCompiled[$rule->getBundle()][$rule->getController()][$rule->getAction()] = true;
    }

    protected function addDeniedRule(RuleTransfer $rule): void
    {
        $this->deniedRules[] = $rule;
        $this->deniedCompiled[$rule->getBundle()][$rule->getController()][$rule->getAction()] = true;
    }

    /**
     * @return array<\Generated\Shared\Transfer\RuleTransfer>
     */
    public function getAllowedRules()
    {
        return $this->allowedRules;
    }

    /**
     * @return array<\Generated\Shared\Transfer\RuleTransfer>
     */
    public function getDeniedRules()
    {
        return $this->deniedRules;
    }

    /**
     * @param \Generated\Shared\Transfer\RuleTransfer $rule
     * @param string $bundle
     * @param string $controller
     * @param string $action
     *
     * @return bool
     */
    public function assert(RuleTransfer $rule, $bundle, $controller, $action)
    {
        $wildcard = AclConstants::VALIDATOR_WILDCARD;

        $ruleBundle = $rule->getBundle();
        if ($ruleBundle !== $wildcard && $ruleBundle !== $bundle) {
            return false;
        }

        $ruleController = $rule->getController();
        if ($ruleController !== $wildcard && $ruleController !== $controller) {
            return false;
        }

        $ruleAction = $rule->getAction();
        if ($ruleAction !== $wildcard && $ruleAction !== $action) {
            return false;
        }

        return true;
    }

    protected function isAssertDenied(string $bundle, string $controller, string $action): bool
    {
        if (isset($this->deniedCompiled[AclConstants::VALIDATOR_WILDCARD]) || isset($this->deniedCompiled[$bundle][$controller][$action])) {
            return true;
        }

        if (isset($this->deniedCompiled[$bundle][AclConstants::VALIDATOR_WILDCARD])) {
            return true;
        }

        if (isset($this->deniedCompiled[$bundle][$controller][AclConstants::VALIDATOR_WILDCARD])) {
            return true;
        }

        return false;
    }

    protected function isAssertAllowed(string $bundle, string $controller, string $action): bool
    {
        $wildcard = AclConstants::VALIDATOR_WILDCARD;

        if (isset($this->allowedCompiled[$bundle][$controller][$action]) || isset($this->allowedCompiled[$wildcard][$wildcard][$wildcard])) {
            return true;
        }

        if (isset($this->allowedCompiled[$bundle][$controller][$wildcard])) {
            return true;
        }

        if (isset($this->allowedCompiled[$bundle][$wildcard][$action]) || isset($this->allowedCompiled[$bundle][$wildcard][$wildcard])) {
            return true;
        }

        if (
            isset($this->allowedCompiled[$wildcard][$controller][$action])
            || isset($this->allowedCompiled[$wildcard][$controller][$wildcard])
            || isset($this->allowedCompiled[$wildcard][$wildcard][$action])
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param string $bundle
     * @param string $controller
     * @param string $action
     *
     * @return bool
     */
    public function isAccessible($bundle, $controller, $action)
    {
        if ($this->isAssertDenied($bundle, $controller, $action)) {
            return false;
        }

        if ($this->isAssertAllowed($bundle, $controller, $action)) {
            return true;
        }

        return false;
    }
}
