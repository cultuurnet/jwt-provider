desc 'Build binaries'
task :build do |task|
  system('composer2 install --no-dev --ignore-platform-reqs --prefer-dist --optimize-autoloader') or exit 1

  FileUtils.rm('web/jwt-example.php', :force => true)
  FileUtils.rm('config.dist.yml', :force => true)
  FileUtils.mkdir('log')
end
