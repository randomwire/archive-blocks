import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { attributes, setAttributes } ) {
    const { numberOfPosts, orderMode, timePeriod, exclude } = attributes;
    const blockProps = useBlockProps();

    return (
        <>
            <InspectorControls>
                <PanelBody title={ __( 'Settings', 'archive-blocks' ) }>
                    <RangeControl
                        __nextHasNoMarginBottom
                        __next40pxDefaultSize
                        label={ __( 'Number of Posts', 'archive-blocks' ) }
                        value={ numberOfPosts }
                        onChange={ ( value ) => setAttributes( { numberOfPosts: value } ) }
                        min={ 1 }
                        max={ 100 }
                    />
                    <SelectControl
                        __nextHasNoMarginBottom
                        __next40pxDefaultSize
                        label={ __( 'Order', 'archive-blocks' ) }
                        value={ orderMode }
                        options={ [
                            { label: __( 'Popular', 'archive-blocks' ), value: 'popular' },
                            { label: __( 'Random', 'archive-blocks' ), value: 'random' },
                        ] }
                        onChange={ ( value ) => setAttributes( { orderMode: value } ) }
                    />
                    <SelectControl
                        __nextHasNoMarginBottom
                        __next40pxDefaultSize
                        label={ __( 'Time Period', 'archive-blocks' ) }
                        value={ timePeriod }
                        options={ [
                            { label: __( '7 days', 'archive-blocks' ), value: '7' },
                            { label: __( '30 days', 'archive-blocks' ), value: '30' },
                            { label: __( '365 days', 'archive-blocks' ), value: '365' },
                            { label: __( 'All time', 'archive-blocks' ), value: '0' },
                        ] }
                        onChange={ ( value ) => setAttributes( { timePeriod: value } ) }
                    />
                    <TextControl
                        __nextHasNoMarginBottom
                        __next40pxDefaultSize
                        label={ __( 'Exclude Posts', 'archive-blocks' ) }
                        help={ __( 'Comma-separated list of post IDs to exclude', 'archive-blocks' ) }
                        value={ exclude }
                        onChange={ ( value ) => setAttributes( { exclude: value } ) }
                    />
                </PanelBody>
            </InspectorControls>
            <div { ...blockProps }>
                <ServerSideRender
                    block="archive-blocks/popular-posts"
                    attributes={ attributes }
                />
            </div>
        </>
    );
}
