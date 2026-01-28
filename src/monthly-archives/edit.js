import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { attributes, setAttributes } ) {
    const { style, divider, showPostCount } = attributes;
    const blockProps = useBlockProps();

    const styleOptions = [
        { label: __( 'Abbreviation (Jan Feb Mar...)', 'archive-blocks' ), value: 'abbreviation' },
        { label: __( 'Initial (J F M...)', 'archive-blocks' ), value: 'initial' },
        { label: __( 'Numeric (01 02 03...)', 'archive-blocks' ), value: 'numeric' },
    ];

    return (
        <>
            <InspectorControls>
                <PanelBody title={ __( 'Archive Settings', 'archive-blocks' ) }>
                    <SelectControl
                        __nextHasNoMarginBottom
                        __next40pxDefaultSize
                        label={ __( 'Month Display Style', 'archive-blocks' ) }
                        value={ style }
                        options={ styleOptions }
                        onChange={ ( value ) => setAttributes( { style: value } ) }
                    />
                    <TextControl
                        __nextHasNoMarginBottom
                        __next40pxDefaultSize
                        label={ __( 'Month Divider', 'archive-blocks' ) }
                        value={ divider }
                        onChange={ ( value ) => setAttributes( { divider: value } ) }
                    />
                    <ToggleControl
                        __nextHasNoMarginBottom
                        label={ __( 'Show number of posts', 'archive-blocks' ) }
                        checked={ showPostCount }
                        onChange={ ( value ) => setAttributes( { showPostCount: value } ) }
                    />
                </PanelBody>
            </InspectorControls>
            <div { ...blockProps }>
                <ServerSideRender
                    block="archive-blocks/monthly-archives"
                    attributes={ attributes }
                />
            </div>
        </>
    );
}
