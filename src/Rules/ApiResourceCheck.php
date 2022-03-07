<?php
declare(strict_types=1);

namespace M10c\Phpstan\Rules;

use PHPStan\Analyser\Scope;
use PhpParser\Node;
use PHPStan\Rules\RuleErrorBuilder;
use Webmozart\Assert\Assert;
use function array_key_exists;
use function array_push;

/**
 * Checks denormalization, normalization context and collection and item operations are set on ApiResources.
 *
 * @implements \PHPStan\Rules\Rule<Node\Stmt\Class_>
 */
class ApiResourceCheck implements \PHPStan\Rules\Rule
{
    public function getNodeType(): string
    {
        return \PhpParser\Node\Stmt\Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];

        Assert::isInstanceOf($node, Node\Stmt\ClassLike::class);

        $class = (string) $node->namespacedName;

        Assert::classExists($class);
        $classReflection = new \ReflectionClass($class);

        $classAttributes = $classReflection->getAttributes();

        foreach ($classAttributes as $attribute) {
            $attributeName = $attribute->getName();

            if ($attributeName === 'ApiPlatform\Core\Annotation\ApiResource') {

                $attributeArgs = $attribute->getArguments();

                if (!array_key_exists('collectionOperations', $attributeArgs)) {
                    array_push($errors, RuleErrorBuilder::message(
                        "Collection operations is not set in class {$class}."
                    )->build());
                }

                if (!array_key_exists('itemOperations', $attributeArgs)) {
                    array_push($errors, RuleErrorBuilder::message(
                        "Item operations is not set in class {$class}."
                    )->build());
                }

                if (!array_key_exists('denormalizationContext', $attributeArgs)) {
                    array_push($errors, RuleErrorBuilder::message(
                        "Denormalization context is not set in class {$class}."
                    )->build());
                }

                if (!array_key_exists('normalizationContext', $attributeArgs)) {
                    array_push($errors, RuleErrorBuilder::message(
                        "Normalization context is not set in class {$class}."
                    )->build());
                }

            }

        }

        return $errors;
    }

}
