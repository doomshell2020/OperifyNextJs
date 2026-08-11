const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('production_sheet', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    datefrom: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    machines_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    totalconsumption: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    actualconsumption: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    confirmedby: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    difference: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    updated: {
      type: DataTypes.DATEONLY,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'production_sheet',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
