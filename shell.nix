with import <nixpkgs> {};
pkgs.mkShell {
  buildInputs = with pkgs; [
    nodejs_24
    nodePackages.npm
    php84
    php84Packages.composer
  ];

  shellHook = ''
    alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
  '';
}
