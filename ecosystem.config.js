module.exports = {
  apps: [
    {
      name: "php-fpm",
      script: "/var/www/scripts/startPhp.sh",
      exec_mode: "fork",
    },
    {
      name: "nginx",
      script: "/var/www/scripts/startNginx.sh",
      exec_mode: "fork",
    },
  ],
};