import grpc
import producto_pb2
import producto_pb2_grpc

with grpc.insecure_channel('127.0.0.1:50051') as canal:
    stub = producto_pb2_grpc.ProductoServiceStub(canal)

    try:
        producto = stub.ObtenerProducto(
            producto_pb2.ProductoRequest(id=3)
        )
        print(producto)
    except grpc.RpcError as error:
        print(error.code(), error.details())
        