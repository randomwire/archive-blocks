import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
    PanelBody,
    RadioControl,
    CheckboxControl,
    ToggleControl,
    TextControl,
    Spinner,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import ServerSideRender from '@wordpress/server-side-render';
import { useState, useEffect } from '@wordpress/element';

export default function Edit( { attributes, setAttributes } ) {
    const {
        filterMode,
        includedCategories,
        excludedCategories,
        showAll,
        allButtonUrl,
        allButtonLabel,
    } = attributes;
    const blockProps = useBlockProps();

    // Force refresh counter to bust SSR cache
    const [ refreshKey, setRefreshKey ] = useState( 0 );
    useEffect( () => {
        setRefreshKey( ( prev ) => prev + 1 );
    }, [ filterMode, showAll, allButtonLabel, JSON.stringify( includedCategories ), JSON.stringify( excludedCategories ) ] );

    const categories = useSelect( ( select ) => {
        return select( 'core' ).getEntityRecords( 'taxonomy', 'category', {
            per_page: -1,
            hide_empty: true,
        } );
    }, [] );

    const isLoading = categories === null;

    const handleCategoryToggle = ( categoryId, isChecked ) => {
        if ( filterMode === 'include' ) {
            const newIncluded = isChecked
                ? [ ...includedCategories, categoryId ]
                : includedCategories.filter( ( id ) => id !== categoryId );
            setAttributes( { includedCategories: newIncluded } );
        } else if ( filterMode === 'exclude' ) {
            const newExcluded = isChecked
                ? [ ...excludedCategories, categoryId ]
                : excludedCategories.filter( ( id ) => id !== categoryId );
            setAttributes( { excludedCategories: newExcluded } );
        }
    };

    const isCategorySelected = ( categoryId ) => {
        if ( filterMode === 'include' ) {
            return includedCategories.includes( categoryId );
        } else if ( filterMode === 'exclude' ) {
            return excludedCategories.includes( categoryId );
        }
        return false;
    };

    return (
        <>
            <InspectorControls>
                <PanelBody title={ __( 'Category Selection', 'archive-blocks' ) }>
                    <RadioControl
                        label={ __( 'Filter Mode', 'archive-blocks' ) }
                        selected={ filterMode }
                        options={ [
                            { label: __( 'Show all categories', 'archive-blocks' ), value: 'all' },
                            { label: __( 'Include only selected', 'archive-blocks' ), value: 'include' },
                            { label: __( 'Exclude selected', 'archive-blocks' ), value: 'exclude' },
                        ] }
                        onChange={ ( value ) => setAttributes( { filterMode: value } ) }
                    />
                    { ( filterMode === 'include' || filterMode === 'exclude' ) && (
                        <div style={ { marginTop: '16px' } }>
                            <p style={ { marginBottom: '8px', fontWeight: '600' } }>
                                { filterMode === 'include'
                                    ? __( 'Select categories to include:', 'archive-blocks' )
                                    : __( 'Select categories to exclude:', 'archive-blocks' )
                                }
                            </p>
                            { isLoading && <Spinner /> }
                            { categories && categories.map( ( category ) => (
                                <CheckboxControl
                                    __nextHasNoMarginBottom
                                    key={ category.id }
                                    label={ category.name }
                                    checked={ isCategorySelected( category.id ) }
                                    onChange={ ( isChecked ) =>
                                        handleCategoryToggle( category.id, isChecked )
                                    }
                                />
                            ) ) }
                        </div>
                    ) }
                </PanelBody>
                <PanelBody title={ __( '"All" Button Settings', 'archive-blocks' ) }>
                    <ToggleControl
                        __nextHasNoMarginBottom
                        label={ __( 'Show "All" button', 'archive-blocks' ) }
                        checked={ showAll }
                        onChange={ ( value ) => setAttributes( { showAll: value } ) }
                    />
                    { showAll && (
                        <>
                            <TextControl
                                __nextHasNoMarginBottom
                                __next40pxDefaultSize
                                label={ __( 'Button Label', 'archive-blocks' ) }
                                value={ allButtonLabel }
                                onChange={ ( value ) => setAttributes( { allButtonLabel: value } ) }
                            />
                            <TextControl
                                __nextHasNoMarginBottom
                                __next40pxDefaultSize
                                label={ __( 'Button URL', 'archive-blocks' ) }
                                help={ __( 'Leave empty to use the homepage URL', 'archive-blocks' ) }
                                value={ allButtonUrl }
                                onChange={ ( value ) => setAttributes( { allButtonUrl: value } ) }
                            />
                        </>
                    ) }
                </PanelBody>
            </InspectorControls>
            <div { ...blockProps }>
                <ServerSideRender
                    key={ refreshKey }
                    block="archive-blocks/category-nav-buttons"
                    attributes={ attributes }
                    httpMethod="POST"
                />
            </div>
        </>
    );
}
