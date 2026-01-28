import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { attributes, setAttributes } ) {
    const { maxPosts, emptyMessage } = attributes;
    const blockProps = useBlockProps();

    const maxPostsOptions = [
        { label: '1', value: 1 },
        { label: '2', value: 2 },
        { label: '3', value: 3 },
        { label: '5', value: 5 },
        { label: '10', value: 10 },
    ];

    return (
        <>
            <InspectorControls>
                <PanelBody title={ __( 'Settings', 'archive-blocks' ) }>
                    <SelectControl
                        __nextHasNoMarginBottom
                        __next40pxDefaultSize
                        label={ __( 'Max Number of Posts', 'archive-blocks' ) }
                        value={ maxPosts }
                        options={ maxPostsOptions }
                        onChange={ ( value ) => setAttributes( { maxPosts: parseInt( value, 10 ) } ) }
                    />
                    <TextControl
                        __nextHasNoMarginBottom
                        __next40pxDefaultSize
                        label={ __( 'Empty Message', 'archive-blocks' ) }
                        value={ emptyMessage }
                        onChange={ ( value ) => setAttributes( { emptyMessage: value } ) }
                        help={ __( 'Message shown when no posts were published on this day.', 'archive-blocks' ) }
                    />
                </PanelBody>
            </InspectorControls>
            <div { ...blockProps }>
                <ServerSideRender
                    block="archive-blocks/on-this-day"
                    attributes={ attributes }
                />
            </div>
        </>
    );
}
