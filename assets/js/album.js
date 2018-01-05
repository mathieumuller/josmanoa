/*global $, Ajax */
function toggleCoverAttributes($mediaCard, toggle) {
    "use strict";
    if (true === toggle) {
        $mediaCard.addClass('media-album-cover');
    } else {
        $mediaCard.removeClass('media-album-cover');
    }

    $mediaCard.find(".btn-album-cover").prop('disabled', toggle);
}

function switchAlbumCover($newCover) {
    "use strict";
    // Remove the previous media cover styles
    toggleCoverAttributes($('.media-album-cover'), false);
    toggleCoverAttributes($newCover, true);
}


$(function () {
    "use strict";

    /**
     * Change the cover of an album
     */
    $("#lightgallery").on("click", ".btn-album-cover", function () {
        var $that = $(this);
        Ajax.post(
            $that.data('url'),
            {
                'done': function () {
                    switchAlbumCover($that.closest('.media-card'));
                }

            }
        );
    });

    /**
     * Removes a media from an album
     */
    $("#lightgallery").on("click", ".btn-album-remove-media", function () {
        var $that = $(this);
        Ajax.post(
            $that.data('url'),
            {
                'done': function () {
                    $that.closest('.media-card').remove();
                }

            }
        );
    });
});