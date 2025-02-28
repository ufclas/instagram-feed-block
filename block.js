(function () {
    var el = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.editor.InspectorControls;
    var TextControl = wp.components.TextControl;
    var RichText = wp.editor.RichText;
	  
    registerBlockType('instagram-feed-block/instagram-block', {
        title: 'Instagram Feed',
        icon: 'format-gallery',
        category: 'common',
        attributes: {
            accessToken: {
                type: 'string',
                default: '',
            },
            limit: {
                type: 'number',
                default: 4,
            },
            previewContent: {
                type: 'string',
                source: 'html',
                selector: '.instagram-preview-content',
            },
            sectionHeading: {
                type: 'string',
                default: '',
            },
            fburl: {
                type: 'string',
                default: '#',
            },
            twitterurl: {
                type: 'string',
                default: '#',
            },
            instagramurl: {
                type: 'string',
                default: '#',
            },
            youtubeurl: {
                type: 'string',
                default: '#',
            },
        },
        edit: function (props) {
            var attributes = props.attributes;

            function onChangeAccessToken(newAccessToken) {
                props.setAttributes({ accessToken: newAccessToken });
            }

            function onChangeLimit(newLimit) {
                props.setAttributes({ limit: parseInt(newLimit) || 4 });

                // Update previewContent on limit change.
                updatePreviewContent();
            }

            function onChangeSectionHeading(newValue) {
                props.setAttributes({ sectionHeading: newValue });
            }

            function onChangeFacebookUrl(newValue) {
                props.setAttributes({ fburl: newValue });
            }

            function onChangeTwitterUrl(newValue) {
                props.setAttributes({ twitterurl: newValue });
            }

            function onChangeInstagramUrl(newValue) {
                props.setAttributes({ instagramurl: newValue });
            }

            function onChangeYoutubeUrl(newValue) {
                props.setAttributes({ youtubeurl: newValue });
            }

            // Function to update previewContent based on API call result.
            function updatePreviewContent() {
                // Make the API call to get the result.
                fetchInstagramPosts(attributes.accessToken, attributes.limit, attributes)
                    .then((result) => {
                        props.setAttributes({ previewContent: result });
                    })
                    .catch((error) => {
                        console.error('Error fetching Instagram posts:', error);
                        // Handle errors gracefully if needed.
                    });
            }

            // Initial update of previewContent when the block is loaded.
            updatePreviewContent();

            return [
                el(InspectorControls, { key: 'inspector' },
                    el('div', { className: 'instagram-settings' },
                        el(TextControl, {
                            label: 'Access Token',
                            value: attributes.accessToken,
                            onChange: onChangeAccessToken,
                        }),
                        el(TextControl, {
                            label: 'Limit',
                            type: 'number',
                            value: attributes.limit,
                            onChange: onChangeLimit,
                        }),
                        el(TextControl, {
                            label: 'Section Heading',
                            value: attributes.sectionHeading,
                            onChange: onChangeSectionHeading,
                        }),
                        el(TextControl, {
                            label: 'Facebook Page Url',
                            value: attributes.fburl,
                            onChange: onChangeFacebookUrl,
                        }),
                        el(TextControl, {
                            label: 'Twitter Page Url',
                            value: attributes.twitterurl,
                            onChange: onChangeTwitterUrl,
                        }),
                        el(TextControl, {
                            label: 'Instagram Page Url',
                            value: attributes.instagramurl,
                            onChange: onChangeInstagramUrl,
                        }),
                        el(TextControl, {
                            label: 'Youtube Page Url',
                            value: attributes.youtubeurl,
                            onChange: onChangeYoutubeUrl,
                        })
                    )
                ),
                el('div', { className: 'instagram-preview' },
                    el(RichText, {
                        tagName: 'div',
                        //value: attributes.previewContent,
						dangerouslySetInnerHTML: { __html: attributes.previewContent },

                        className: 'instagram-preview-content',
                        onChange: function (newContent) {
                            props.setAttributes({ previewContent: newContent });
                        },
                    })
                ),
            ];
        },
        save: function () {
            return null;
        },
    });

    // Function to fetch Instagram posts.
    function fetchInstagramPosts(accessToken, limit, attributes) {
        const api_url = `https://graph.instagram.com/v12.0/me/media?fields=id,caption,media_type,media_url,thumbnail_url,permalink,timestamp&access_token=${accessToken}&limit=${limit}`;

        return fetch(api_url)
            .then(response => {
                if (!response.ok) {
                    console.error('Network response was not ok:', response);
                    throw new Error(`Network response was not ok. Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.data) {
                    return generateHtml(data.data, attributes);  // Pass attributes here
                } else {
                    console.error('No data found in Instagram response:', data);
                    return '<p>No Instagram posts found.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching Instagram posts:', error);
                return `<p>Error fetching Instagram posts. Please check your access token and try again. ${error.message}</p>`;
            });
    }

    // Function to generate HTML from Instagram posts data.
   function generateHtml(posts, attributes) {
    let html = '<section class="social-media-shell"><div class="social-media-shell-wrapper"><p class="social-media-shell-title">' + attributes.sectionHeading + '</p><div class="social-feed row">';

    posts.forEach(post => {
        const caption = post.caption ? post.caption : '';
        const media = (post.media_type === 'IMAGE' || post.media_type === 'CAROUSEL_ALBUM') ? escUrl(post.media_url) : escUrl(post.thumbnail_url);

        html += `<div class="col-xl-3 col-md-6 col-12 soc-container">
            <a href="${escUrl(post.permalink)}" target="_blank" class="social-item m-insta social-overlay" style="background-image: url(${media});">
                <span class="social-text">${caption}<span class="social-icon"><img src="/wp-content/themes/ufl-main-uni/img/icon-instagram.png" alt="Instagram Icon"></span></span>
            </a>
        </div>`;
    });

    html += '</div>';
    html += '<!-- START SOCIAL FEED ICON COL -->';
    html += '<div class="col-12 social-column social-column-blue justify-content-center mt-5">';


html += '<div class="col-12 social-column social-column-blue justify-content-center mt-5">';
// Facebook Icon
    var fburl = attributes.fburl;
    if (fburl && fburl !== '#') {
        html += `<a href="${attributes.fburl}" target="_blank" class="facebook-icon" rel="noopener">
            <i class="fa-brands fa-facebook-f"></i>
            <span class="visually-hidden">Facebook Icon</span>
        </a>`;
    }

    // Twitter Icon
    var twitterurl = attributes.twitterurl;
    if (twitterurl && twitterurl !== '#') {
        html += `<a href="${attributes.twitterurl}" target="_blank" class="twitter-icon" rel="noopener">
            <i class="fa-brands fa-x-twitter"></i>
            <span class="visually-hidden">Twitter Icon</span>
        </a>`;
    }

    // Instagram Icon
    var instagramurl = attributes.instagramurl;
    if (instagramurl && instagramurl !== '#') {
        html += `<a href="${attributes.instagramurl}" target="_blank" class="instagram-icon" rel="noopener">
            <i class="fa-brands fa-instagram"></i>
            <span class="visually-hidden">Instagram Icon</span>
        </a>`;
    }

    // Youtube Icon
    var youtubeurl = attributes.youtubeurl;
    if (youtubeurl && youtubeurl !== '#') {
        html += `<a href="${attributes.youtubeurl}" target="_blank" class="youtube-icon" rel="noopener">
            <i class="fa-brands fa-youtube"></i>
            <span class="visually-hidden">Youtube Icon</span>
        </a>`;
    }


    html += '</div>';
    html += '</div></section>';

    return html;
}


    // Function to escape HTML entities.
    function escHtml(html) {
        return wp.element.RawHTML('<span>' + html + '</span>').toString();
    }

    // Function to escape URL.
    function escUrl(url) {
        return wp.url.addQueryArgs({ href: url }).href;
    }
})();
