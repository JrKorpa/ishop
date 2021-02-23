package main

import (
	"kela_im"
)

func main() {
	server := kela_im.NewSocketIoServer(3000, false)
	server.Run()
}
