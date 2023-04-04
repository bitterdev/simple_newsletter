/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

if (typeof CKEDITOR !== 'undefined') {
    CKEDITOR.plugins.addExternal('newsletterplaceholders', CCM_REL + '/packages/simple_newsletter/js/ckeditor4/plugins/newsletterplaceholders/');
}