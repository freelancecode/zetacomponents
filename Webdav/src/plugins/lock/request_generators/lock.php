<?php
/**
 * File containing the ezcWebdavLockLockRequestGenerator class.
 *
 * @package Webdav
 * @version //autogentag//
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Request generator used to generate PROPPATCH requests to realize the LOCK.
 *
 * @package Webdav
 * @version //autogen//
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
class ezcWebdavLockLockRequestGenerator implements ezcWebdavLockRequestGenerator
{
    /**
     * Generated requests. 
     * 
     * @var array(ezcWebdavPropPatchRequest)
     */
    protected $requests = array();

    /**
     * Request that issued the lock. 
     * 
     * @var ezcWebdavLockRequest
     */
    protected $issueingRequest;

    /**
     * Active lock information part of lock response.
     * 
     * @var ezcWebdavLockDiscoveryPropertyActiveLock
     */
    protected $activeLock;

    /**
     * Creates a new request generator.
     * 
     * @param ezcWebdavLockRequest $request 
     * @param string $lockToken 
     */
    public function __construct(
        ezcWebdavLockRequest $request,
        ezcWebdavLockDiscoveryPropertyActiveLock $activeLock
    )
    {
        $this->issueingRequest = $request;
        $this->activeLock      = $activeLock;
    }

    /**
     * Notify the generator about a response.
     *
     * Notifies the request generator that a request should be generated w.r.t.
     * the given $response.
     * 
     * @param ezcWebdavPropFindResponse $propFind 
     * @return void
     */
    public function notify( ezcWebdavPropFindResponse $response )
    {
        $currentLockDiscoveryProp = null;

        // Receive the current lockdiscovery property to update it
        foreach ( $response->responses as $propStatRes )
        {
            if ( $propStatRes->status === ezcWebdavResponse::STATUS_200 &&
                 $propStatRes->storage->contains( 'lockdiscovery' )
               )
            {
                // Property found no further searching
                // Clone current state to avoid consistency problems
                $currentLockDiscoveryProp = clone $propStatRes->storage->get( 'lockdiscovery' );
                break;
            }
            if ( $propStatRes->status === ezcWebdavResponse::STATUS_404 &&
                 $propStatRes->storage->contains( 'lockdiscovery' )
               )
            {
                // Property definitly not found, no further searching
                break;
            }
        }

        // If no lockdiscovery property has been found, yet, create a new one
        if ( $currentLockDiscoveryProp === null )
        {
            $currentLockDiscoveryProp = new ezcWebdavLockDiscoveryProperty();
        }
        // Clone active lock to avoid consitency problems
        $currentLockDiscoveryProp->activeLock->append( clone $this->activeLock );
        
        // PropPatch request to update resource property
        $propPatch = new ezcWebdavPropPatchRequest( $response->node->path );
        $propPatch->updates->attach(
            $currentLockDiscoveryProp,
            ezcWebdavPropPatchRequest::SET
        );

        $this->requests[] = $propPatch;
    }

    /**
     * Returns all collected requests generated in the processor. 
     * 
     * @return array(ezcWebdavRequest)
     */
    public function getRequests()
    {
        return $this->requests;
    }
}

?>
