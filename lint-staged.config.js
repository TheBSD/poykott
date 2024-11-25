export default {
    '**/*': 'prettier --write --ignore-unknown',
    '**/*.php*': ['vendor/bin/duster fix'],
};
