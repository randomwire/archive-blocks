import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { attributes, setAttributes } ) {
    const { taxonomy, count, orderBy, exclude, separator } = attributes;
    const blockProps = useBlockProps();

    const taxonomyOptions = [
        { label: __( 'Categories', 'archive-blocks' ), value: 'category' },
        { label: __( 'Tags', 'archive-blocks' ), value: 'post_tag' },
    ];

    const countOptions = [
        { label: '5', value: 5 },
        { label: '10', value: 10 },
        { label: '25', value: 25 },
        { label: '50', value: 50 },
        { label: '100', value: 100 },
    ];

    const orderByOptions = [
        { label: __( 'Most Popular', 'archive-blocks' ), value: 'popular' },
        { label: __( 'Alphabetical', 'archive-blocks' ), value: 'name' },
    ];

    return (
        <>
            <InspectorControls>
                <PanelBody title={ __( 'Popular Terms Settings', 'archive-blocks' ) }>
                    <SelectControl
                        label={ __( 'Taxonomy', 'archive-blocks' ) }
                        value={ taxonomy }
                        options={ taxonomyOptions }
                        onChange={ ( value ) => setAttributes( { taxonomy: value } ) }
                    />
                    <SelectControl
                        label={ __( 'Number of Terms', 'archive-blocks' ) }
                        value={ count }
                        options={ countOptions }
                        onChange={ ( value ) => setAttributes( { count: parseInt( value, 10 ) } ) }
                    />
                    <SelectControl
                        label={ __( 'Order By', 'archive-blocks' ) }
                        value={ orderBy }
                        options={ orderByOptions }
                        onChange={ ( value ) => setAttributes( { orderBy: value } ) }
                    />
                    <TextControl
                        label={ __( 'Exclude Terms', 'archive-blocks' ) }
                        help={ __( 'Comma-separated list of term names to exclude', 'archive-blocks' ) }
                        value={ exclude }
                        onChange={ ( value ) => setAttributes( { exclude: value } ) }
                    />
                    <TextControl
                        label={ __( 'Separator', 'archive-blocks' ) }
                        value={ separator }
                        onChange={ ( value ) => setAttributes( { separator: value } ) }
                    />
                </PanelBody>
            </InspectorControls>
            <div { ...blockProps }>
                <ServerSideRender
                    block="archive-blocks/popular-terms"
                    attributes={ attributes }
                />
            </div>
        </>
    );
}
