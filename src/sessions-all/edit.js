/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";

import {
	Panel,
	PanelBody,
	PanelRow,
	TextControl,
    ColorPalette,
} from "@wordpress/components";

import ServerSideRender from "@wordpress/server-side-render";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit ({ attributes, setAttributes }) {

    const onChangeTitle = (title) => {
        setAttributes({ title });
    };

	const blockProps = useBlockProps();

    return (
		<div {...blockProps}> {/* Wrap everything in a div to ensure valid JSX */}
            <InspectorControls>
                <Panel>
                    <PanelBody title={__("Settings", "wpdev")} initialOpen={true}>
                    <PanelRow>
                        <TextControl
                            label="Session Date"
                            value={ attributes.scheduleDate }
                            onChange={ ( value ) => setAttributes( { scheduleDate: value } ) }
                            type="text"
                        />
                    </PanelRow>
                    <PanelRow>
                        <TextControl
                            label="Start Time"
                            value={ attributes.startTime }
                            onChange={ ( value ) => setAttributes( { startTime: value } ) }
                            type="text"
                        />
                    </PanelRow>
                    <PanelRow>
                        <TextControl
                            label="End Time"
                            value={ attributes.endTime }
                            onChange={ ( value ) => setAttributes( { endTime: value } ) }
                            type="text"
                        />
                    </PanelRow>
                    </PanelBody>
                    <PanelBody title={__('Background Color')}>
                        <ColorPalette
                            value={attributes.backgroundColor}
                            onChange={(color) => setAttributes({ backgroundColor: color })}
                        />
                    </PanelBody>
                    <PanelBody title={__('Link Color')}>
                        <ColorPalette
                            value={attributes.linkColor}
                            onChange={(color) => setAttributes({ linkColor: color })}
                        />
                    </PanelBody>
                    <PanelBody title={__('Meta text Color')}>
                        <ColorPalette
                            value={attributes.metaColor}
                            onChange={(color) => setAttributes({ metaColor: color })}
                        />
                    </PanelBody>
                    <PanelBody title={__('Time text Color')}>
                        <ColorPalette
                            value={attributes.timeColor}
                            onChange={(color) => setAttributes({ timeColor: color })}
                        />
                    </PanelBody>
                </Panel>
            </InspectorControls>
            <ServerSideRender
                block="worldskills/sessions-all"
                attributes={attributes}
            />
        </div>
    );
}


