<?php
namespace TYPO3\TYPO3CR\Eel\FlowQueryOperations;

/*
 * This file is part of the TYPO3.TYPO3CR package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Eel\FlowQuery\FlowQuery;
use TYPO3\Eel\FlowQuery\Operations\AbstractOperation;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * "nextAll" operation working on TYPO3CR nodes. It iterates over all
 * context elements and returns each following sibling or only those matching
 * the filter expression specified as optional argument.
 */
class NextAllOperation extends AbstractOperation
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected static $shortName = 'nextAll';

    /**
     * {@inheritdoc}
     *
     * @var integer
     */
    protected static $priority = 0;

    /**
     * {@inheritdoc}
     *
     * @param array (or array-like object) $context onto which this operation should be applied
     * @return boolean TRUE if the operation can be applied onto the $context, FALSE otherwise
     */
    public function canEvaluate($context)
    {
        return count($context) === 0 || (isset($context[0]) && ($context[0] instanceof NodeInterface));
    }

    /**
     * {@inheritdoc}
     *
     * @param FlowQuery $flowQuery the FlowQuery object
     * @param array $arguments the arguments for this operation
     * @return void
     */
    public function evaluate(FlowQuery $flowQuery, array $arguments)
    {
        $output = array();
        $outputNodePaths = array();
        foreach ($flowQuery->getContext() as $contextNode) {
            $nextNodes = $this->getNextForNode($contextNode);
            if (is_array($nextNodes)) {
                foreach ($nextNodes as $nextNode) {
                    if ($nextNode !== null && !isset($outputNodePaths[$nextNode->getPath()])) {
                        $outputNodePaths[$nextNode->getPath()] = true;
                        $output[] = $nextNode;
                    }
                }
            }
        }
        $flowQuery->setContext($output);

        if (isset($arguments[0]) && !empty($arguments[0])) {
            $flowQuery->pushOperation('filter', $arguments);
        }
    }

    /**
     * @param NodeInterface $contextNode The node for which the preceding node should be found
     * @return NodeInterface The preceding nodes of $contextNode or NULL
     */
    protected function getNextForNode(NodeInterface $contextNode)
    {
        $nodesInContext = $contextNode->getParent()->getChildNodes();
        $count = count($nodesInContext);

        for ($i = 0; $i < $count; $i++) {
            if ($nodesInContext[$i] === $contextNode) {
                unset($nodesInContext[$i]);
                return array_values($nodesInContext);
            } else {
                unset($nodesInContext[$i]);
            }
        }
        return null;
    }
}
