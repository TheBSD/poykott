export default {
    // '**/*': 'prettier --write --ignore-unknown',
    '**/*.php*': ['vendor/bin/rector process', 'vendor/bin/pint --dirty'],
};
