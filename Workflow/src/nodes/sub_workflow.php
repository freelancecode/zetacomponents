<?php
/**
 * File containing the ezcWorkflowNodeSubWorkflow class.
 *
 * @package Workflow
 * @version //autogen//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An object of the ezcWorkflowNodeSubWorkflow class represents a sub-workflow.
 *
 * When the node is reached during execution of the workflow, the specified sub-workflow
 * is started. The original workflow is suspended until the sub-workflow has finished executing.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 1
 *
 * @todo Example of build up, example of resume with parameters.
 * @package Workflow
 * @version //autogen//
 */
class ezcWorkflowNodeSubWorkflow extends ezcWorkflowNode
{
    /**
     * Execution ID of the sub workflow,
     * 0 if it has not been started yet.
     *
     * @var integer
     */
    protected $state = 0;

    /**
     * Constructs a new sub workflow with the configuration $configuration.
     *
     * Configuration format
     * String:
     *  The name of the workflow to execute. The workflow is loaded using the
     *  loadByName method on the execution engine.
     *
     * @param mixed $configuration
     * @throws ezcWorkflowDefinitionStorageException
     */
    public function __construct( $configuration )
    {
        parent::__construct( $configuration );
    }

    /**
     * Executes this node.
     *
     * @param ezcWorkflowExecution $execution
     * @ignore
     */
    public function execute( ezcWorkflowExecution $execution )
    {
        if ( $execution->definitionStorage === null )
        {
            throw new ezcWorkflowExecutionException(
              'No ezcWorkflowDefinitionStorage implementation available.'
            );
        }

        $workflow = $execution->definitionStorage->loadByName( $this->configuration );

        // Sub Workflow is not interactive.
        if ( !$workflow->isInteractive() && !$workflow->hasSubWorkflows() )
        {
            $subExecution = $execution->getSubExecution( null, false );
            $subExecution->workflow = $workflow;
            $subExecution->start();
        }
        // Sub Workflow is interactive.
        else
        {
            // Sub Workflow is to be started.
            if ( $this->state == 0 )
            {
                $subExecution = $execution->getSubExecution();
                $subExecution->workflow = $workflow;
                $subExecution->start( $this->id );

                $this->state = $subExecution->getId();
            }
            // Sub Workflow is to be resumed.
            else
            {
                $subExecution = $execution->getSubExecution( $this->state );
                $subExecution->workflow = $workflow;
                $subExecution->resume( $execution->getVariables() );
            }
        }

        // Execution of Sub Workflow has ended.
        if ( $subExecution->hasEnded() )
        {
            $this->activateNode( $execution, $this->outNodes[0] );

            $this->state = 0;

            return parent::execute( $execution );
        }

        // Execution of Sub Workflow has been suspended.
        foreach ( $subExecution->getWaitingFor() as $variableName => $data )
        {
            $execution->addWaitingFor( $this, $variableName, $data['condition'] );
        }

        return false;
    }

    /**
     * Returns a textual representation of this node.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return 'Sub Workflow: ' . $this->configuration;
    }
}
?>
