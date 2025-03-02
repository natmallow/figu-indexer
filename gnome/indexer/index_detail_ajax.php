<?php

$SECURITY->isLoggedIn();

use gnome\classes\model\IndicesLink;

$IndicesLink = new IndicesLink();

// helper function here

if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {

    // gets all the indicies given the group id which is the current selected index
    if ( filter_input( INPUT_POST, 'action' ) == 'get-all-indices' ) {
        header( 'Content-Type: text/html' );
        $id = filter_input( INPUT_POST, 'indices_id' );
        $rsLinkedIndices = $IndicesLink->getLinkedIndices( $id );

        $returnHtml = '';
        foreach ( $rsLinkedIndices as $row ) {

            // Extract values from the associative array
            $indices_id = $row[ 'indices_id' ];
            $index_name = $row[ 'index_name' ];
            $is_linked = $row[ 'is_linked' ];

            $checked = $is_linked? 'checked' : '';
            $returnHtml .= "
            <div class='form-check'>
                <input class='form-check-input' type='checkbox' value='$indices_id' id='flexCheckDefault'
                $checked
                >
                <label class='form-check-label' for='flexCheckDefault'>
                     $index_name
                </label>
            </div>
            ";
        }
        echo $returnHtml;
        exit();
    } elseif ( filter_input( INPUT_POST, 'action' ) == 'add-link-index' ) {
        $indices_group_id = filter_input( INPUT_POST, 'indices_group_id' );
        $id = filter_input( INPUT_POST, 'indices_id' );
        // $IndicesLink->linkIndices( $indices_group_id, $id );
        if ( $IndicesLink->linkIndices( $indices_group_id, $id ) ) {
            echo json_encode( [ 'success' => '1' ] );
        } else {
            echo json_encode( [ 'success' => '0', 'error' => 'Failed to link indices' ] );
        }
        exit();

    } elseif ( filter_input( INPUT_POST, 'action' ) == 'remove-link-index' ) {
        $indices_group_id = filter_input( INPUT_POST, 'indices_group_id' );
        $id = filter_input( INPUT_POST, 'indices_id' );
        // $IndicesLink->unlinkIndices( $indices_group_id, $id );
        if ( $IndicesLink->unlinkIndices( $indices_group_id, $id ) ) {
            echo json_encode( [ 'success' => '1' ] );
        } else {
            echo json_encode( [ 'success' => '0', 'error' => 'Failed to link indices' ] );
        }
        exit();

    } elseif ( filter_input( INPUT_POST, 'action' ) == 'fetch-linked' ) {
        header( 'Content-Type: text/html' );
        $id = filter_input( INPUT_POST, 'indices_id' );
        $rsLinkedIndices = $IndicesLink->getLinkedIndex( $id );

        $returnHtml = "<ul class='list-group-flat'>";
        foreach ( $rsLinkedIndices as $row ) {
            
            // Extract values from the associative array
            $index_name = $row[ 'index_name' ];

            $returnHtml .= "
                <li class='list-group-item list-group-item-warning'>
                     $index_name
                </li>
            ";
        }
        echo $returnHtml .=  '</ul>';
        exit();
    }

}