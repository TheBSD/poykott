export default {
    '**/*': 'npx prettier --write --ignore-unknown',
    '**/*.php*': ['vendor/bin/duster fix'],
};
