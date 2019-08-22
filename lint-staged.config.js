module.exports = {
  '*.php': ['/php-cs-fixer fix --config=./.php_cs --allow-risky=yes', 'git add'],
  '*.md': ['yarn lint:markdownlint', 'yarn lint:prettier', 'git add'],
};
