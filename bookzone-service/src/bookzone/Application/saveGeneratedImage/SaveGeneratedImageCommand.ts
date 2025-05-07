import { VisualizationType } from "src/bookzone/Domain/VisualizationType";


export class SaveGeneratedImageCommand {
  constructor(
    public readonly bookId: string,
    public readonly type: VisualizationType,
    public readonly description: string,
    public readonly imageUrl: string,
  ) { }
}
