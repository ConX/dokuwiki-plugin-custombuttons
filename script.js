jQuery(function() {  // on page load
    let $pretagInput = jQuery('#cb_add_button input[name="pretag"]'),
        $posttagInput = jQuery('#cb_add_button input[name="posttag"]'),
        $sampleInput = jQuery('#cb_add_button input[name="sample"]'),
        $codeInput = jQuery('#cb_add_button input[name="code"]');

    //if pretag or posttag is filled, code text field must be disabled
    $pretagInput.on('keyup', function() {
        let hasPrePostTags = jQuery(this).val() !== '' || $posttagInput.val() !== '';
        $codeInput.prop("disabled", hasPrePostTags);
    });
    $posttagInput.on('keyup', function() {
        let hasPrePostTags = jQuery(this).val() !== '' || $pretagInput.val() !== '';
        $codeInput.prop("disabled", hasPrePostTags);
    });

    //if code is filled, pretag,posttag and sample text field must be disabled
    $codeInput.on('keyup', function() {
        let hasCodeTag = jQuery(this).val() !== '';
        $pretagInput.prop("disabled", hasCodeTag);
        $posttagInput.prop("disabled", hasCodeTag);
        $sampleInput.prop("disabled", hasCodeTag);
    });
});
