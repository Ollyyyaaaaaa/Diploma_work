module.exports = [
    {
        ignores: [
            'node_modules/',
            'package-lock.json',
            'src/test-data/*.json',
            'src/mochaReports/',
            'src/reports/'
        ]
    },
    {
        rules: {
            'no-new': 'warn',
            'no-console': 'off',
            'linebreak-style': 'off',
            'no-await-in-loop': 'off',
            'no-param-reassign': 'off',
            'object-curly-newline': 'off',
            'no-use-before-define': 'off',
            'no-restricted-syntax': 'off',
            'mocha/no-setup-in-describe': 'off',
            'comma-dangle': ['error', 'never'],
            'space-before-function-paren': 'off',
            'lines-between-class-members': 'off',
            radix: 'off',
            semi: ['error', 'always'],
            quotes: ['error', 'single'],
            indent: ['error', 4, { SwitchCase: 1 }],
            'no-unused-vars': [
                'error',
                {
                    argsIgnorePattern: '^h$',
                    varsIgnorePattern: '^h$'
                }
            ],
            'max-len': [
                'error',
                {
                    code: 140,
                    ignoreUrls: true,
                    ignoreComments: true,
                    ignoreRegExpLiterals: true,
                    ignoreTrailingComments: true,
                    ignoreTemplateLiterals: true,
                    ignorePattern: '(^import\\s.+\\sfrom\\s.+;$)|(\\}\\sfrom\\s.+;$)'
                }
            ],

            'mocha/no-mocha-arrows': 'off'
        },
        languageOptions: {
            ecmaVersion: 'latest',
            globals: {
                node: true,
                es2021: true,
                browser: true,
                commonjs: true
            }
        }
    }
];
